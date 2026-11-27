<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Vehicle;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $query = Vehicle::where('organisation_id', $organisation->id)
            ->with('documents')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration', 'like', "%{$search}%")
                  ->orWhere('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->paginate(20)->withQueryString();

        $statusCounts = Vehicle::where('organisation_id', $organisation->id)
            ->select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('operations.fleet.index', compact('vehicles', 'statusCounts'));
    }

    public function create()
    {
        return view('operations.fleet.create');
    }

    public function store(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $validated = $request->validate([
            'registration' => ['required', 'string', 'max:20', 'unique:vehicles,registration'],
            'vin' => ['nullable', 'string', 'max:20'],
            'make' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 1)],
            'vehicle_type' => ['required', Rule::in(['sedan', 'suv', 'minibus', 'ambulance', 'wheelchair_van', 'stretcher_van', 'other'])],
            'capacity_seated' => ['required', 'integer', 'min:0'],
            'capacity_wheelchair' => ['nullable', 'integer', 'min:0'],
            'capacity_stretcher' => ['nullable', 'integer', 'min:0'],
            'fuel_type' => ['required', Rule::in(['petrol', 'diesel', 'hybrid', 'electric', 'other'])],
            'license_expiry' => ['nullable', 'date'],
            'cofc_expiry' => ['nullable', 'date'],
            'insurance_expiry' => ['nullable', 'date'],
            'operating_permit_expiry' => ['nullable', 'date'],
        ]);

        $validated['organisation_id'] = $organisation->id;
        $validated['status'] = 'available';

        $vehicle = Vehicle::create($validated);

        return redirect()->route('operations.fleet.show', $vehicle)
            ->with('success', 'Vehicle added successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorizeOrg($vehicle->organisation_id);
        $vehicle->load('documents.verifier', 'organisation');
        return view('operations.fleet.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeOrg($vehicle->organisation_id);
        return view('operations.fleet.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeOrg($vehicle->organisation_id);

        $validated = $request->validate([
            'registration' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'registration')->ignore($vehicle->id)],
            'make' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 1)],
            'status' => ['required', Rule::in(['available', 'assigned', 'in_service', 'under_maintenance', 'out_of_service', 'compliance_suspended'])],
            'license_expiry' => ['nullable', 'date'],
            'cofc_expiry' => ['nullable', 'date'],
            'insurance_expiry' => ['nullable', 'date'],
            'operating_permit_expiry' => ['nullable', 'date'],
        ]);

        $vehicle->update($validated);

        return redirect()->route('operations.fleet.show', $vehicle)
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeOrg($vehicle->organisation_id);
        $vehicle->delete();
        return redirect()->route('operations.fleet.index')->with('success', 'Vehicle removed.');
    }

    public function uploadDocument(Request $request, Vehicle $vehicle, DocumentService $documents)
    {
        $this->authorizeOrg($vehicle->organisation_id);

        $request->validate([
            'document' => ['required', 'file', 'max:' . DocumentService::MAX_SIZE_KB, 'mimes:pdf,jpg,jpeg,png'],
            'type' => ['required', Rule::in(['vehicle_license', 'cofc', 'insurance', 'operating_permit', 'other'])],
            'expires_at' => ['nullable', 'date'],
        ]);

        $document = $documents->upload(
            $vehicle,
            $request->file('document'),
            $request->type,
            $request->expires_at,
            auth()->id()
        );

        return back()->with('success', 'Document uploaded and verified.');
    }

    public function deleteDocument(Vehicle $vehicle, Document $document, DocumentService $documents)
    {
        $this->authorizeOrg($vehicle->organisation_id);
        $this->authorize('delete', $document);

        if ($document->documentable_id !== $vehicle->id || $document->documentable_type !== $vehicle->getMorphClass()) {
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