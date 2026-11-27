<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Organisation::whereIn('type', ['sos_ems', 'partner'])
            ->orderBy('name')
            ->get();

        return view('operations.partners.index', compact('partners'));
    }

    public function show(Organisation $partner)
    {
        return view('operations.partners.show', compact('partner'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('operations.partners.create', [
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'partner_category' => ['required', 'string', 'in:' . implode(',', array_keys($this->categories()))],
            'email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'admin_first_name' => ['required', 'string', 'max:100'],
            'admin_last_name' => ['required', 'string', 'max:100'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $organisation = Organisation::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'type' => 'sos_ems',
            'partner_category' => $validated['partner_category'],
            'email' => $validated['email'],
            'contact_phone' => $validated['contact_phone'],
            'address' => $validated['address'] ?? null,
            'is_active' => true,
            'commission_frequency' => 'monthly',
        ]);

        $admin = User::create([
            'organisation_id' => $organisation->id,
            'first_name' => $validated['admin_first_name'],
            'last_name' => $validated['admin_last_name'],
            'email' => $validated['admin_email'],
            'phone' => $validated['admin_phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'partner_admin',
            'status' => 'active',
        ]);
        $admin->assignRole(Role::firstOrCreate(['name' => 'partner_admin', 'guard_name' => 'web']));

        return redirect()->route('operations.partners.index')
            ->with('success', "Partner '{$organisation->name}' registered with an administrator account.");
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'super_admin', 403, 'Only the platform super administrator can register partners.');
    }

    private function categories(): array
    {
        return [
            'hospital' => 'Hospital',
            'healthcare_practice' => 'Healthcare Practice',
            'caregiver' => 'Caregiver / Care Agency',
            'transport_partner' => 'Transport Partner',
            'other' => 'Other',
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'partner';
        $slug = $base;

        while (Organisation::where('slug', $slug)->exists()) {
            $slug = $base . '-' . Str::lower(Str::random(5));
        }

        return $slug;
    }
}