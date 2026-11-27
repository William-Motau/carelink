<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Driver;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $query = Driver::where('organisation_id', $organisation->id)
            ->with('user', 'documents')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_number', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $drivers = $query->paginate(20)->withQueryString();

        return view('operations.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('operations.drivers.create');
    }

    public function store(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'employee_number' => ['required', 'string', 'max:30', 'unique:drivers,employee_number'],
            'license_number' => ['required', 'string', 'max:30', 'unique:drivers,license_number'],
            'license_class' => ['required', 'string', 'max:20'],
            'license_expiry' => ['required', 'date'],
            'prdp_expiry' => ['nullable', 'date'],
            'medical_cert_expiry' => ['nullable', 'date'],
            'background_check_expiry' => ['nullable', 'date'],
        ]);

        $user = User::create([
            'organisation_id' => $organisation->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'] ?? ($validated['employee_number'] . '@driver.carelink.local'),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make(bin2hex(random_bytes(8))),
            'role' => 'driver',
            'status' => 'active',
        ]);

        $driver = Driver::create([
            'organisation_id' => $organisation->id,
            'user_id' => $user->id,
            'employee_number' => $validated['employee_number'],
            'license_number' => $validated['license_number'],
            'license_class' => $validated['license_class'],
            'license_expiry' => $validated['license_expiry'],
            'prdp_expiry' => $validated['prdp_expiry'] ?? null,
            'medical_cert_expiry' => $validated['medical_cert_expiry'] ?? null,
            'background_check_expiry' => $validated['background_check_expiry'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('operations.drivers.show', $driver)
            ->with('success', 'Driver added successfully.');
    }

    public function show(Driver $driver)
    {
        $this->authorizeOrg($driver->organisation_id);
        $driver->load('documents.verifier', 'user', 'organisation');
        return view('operations.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        $this->authorizeOrg($driver->organisation_id);
        return view('operations.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $this->authorizeOrg($driver->organisation_id);

        $validated = $request->validate([
            'license_class' => ['required', 'string', 'max:20'],
            'license_expiry' => ['required', 'date'],
            'prdp_expiry' => ['nullable', 'date'],
            'medical_cert_expiry' => ['nullable', 'date'],
            'background_check_expiry' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'suspended', 'expired_docs', 'on_leave'])],
        ]);

        $driver->update($validated);

        return redirect()->route('operations.drivers.show', $driver)
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        $this->authorizeOrg($driver->organisation_id);
        $driver->delete();
        return redirect()->route('operations.drivers.index')->with('success', 'Driver removed.');
    }

    public function uploadDocument(Request $request, Driver $driver, DocumentService $documents)
    {
        $this->authorizeOrg($driver->organisation_id);

        $request->validate([
            'document' => ['required', 'file', 'max:' . DocumentService::MAX_SIZE_KB, 'mimes:pdf,jpg,jpeg,png'],
            'type' => ['required', Rule::in(['driver_license', 'prdp', 'medical_certificate', 'background_check', 'training_certificate', 'other'])],
            'expires_at' => ['nullable', 'date'],
        ]);

        $documents->upload(
            $driver,
            $request->file('document'),
            $request->type,
            $request->expires_at,
            auth()->id()
        );

        return back()->with('success', 'Document uploaded and verified.');
    }

    public function deleteDocument(Driver $driver, Document $document, DocumentService $documents)
    {
        $this->authorizeOrg($driver->organisation_id);
        $this->authorize('delete', $document);

        if ($document->documentable_id !== $driver->id || $document->documentable_type !== $driver->getMorphClass()) {
            abort(404);
        }

        $documents->delete($document);

        return back()->with('success', 'Document removed.');
    }

    protected function authorizeOrg(?int $organisationId): void
    {
        $user = auth()->user();

        if ($user?->role === 'super_admin') {
            return;
        }

        abort_unless($organisationId !== null && $user?->organisation_id === $organisationId, 403);
    }
}