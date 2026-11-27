<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $vehicleIds = Vehicle::where('organisation_id', $organisation->id)->pluck('id');
        $driverIds = Driver::where('organisation_id', $organisation->id)->pluck('id');

        // Expiring documents (within 30 days) and expired documents
        $expiringDocs = Document::query()
            ->where(function ($q) use ($vehicleIds, $driverIds) {
                $q->where(function ($sub) use ($vehicleIds) {
                    $sub->where('documentable_type', Vehicle::class)
                        ->whereIn('documentable_id', $vehicleIds);
                })->orWhere(function ($sub) use ($driverIds) {
                    $sub->where('documentable_type', Driver::class)
                        ->whereIn('documentable_id', $driverIds);
                });
            })
            ->whereNotNull('expires_at')
            ->where('expires_at', '>=', now())
            ->where('expires_at', '<=', now()->addDays(30))
            ->with('documentable')
            ->orderBy('expires_at')
            ->get();

        $expiredDocs = Document::query()
            ->where(function ($q) use ($vehicleIds, $driverIds) {
                $q->where(function ($sub) use ($vehicleIds) {
                    $sub->where('documentable_type', Vehicle::class)
                        ->whereIn('documentable_id', $vehicleIds);
                })->orWhere(function ($sub) use ($driverIds) {
                    $sub->where('documentable_type', Driver::class)
                        ->whereIn('documentable_id', $driverIds);
                });
            })
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with('documentable')
            ->orderBy('expires_at')
            ->get();

        // Vehicle compliance breakdown
        $vehicles = Vehicle::where('organisation_id', $organisation->id)->get();
        $vehicleCompliant = $vehicles->filter(fn ($v) => $v->isCompliant())->count();
        $vehicleNonCompliant = $vehicles->count() - $vehicleCompliant;

        // Driver compliance breakdown
        $drivers = Driver::where('organisation_id', $organisation->id)->get();
        $driverCompliant = $drivers->filter(fn ($d) => $d->isCompliant())->count();
        $driverNonCompliant = $drivers->count() - $driverCompliant;

        $stats = [
            'expiring' => $expiringDocs->count(),
            'expired' => $expiredDocs->count(),
            'vehicle_compliant' => $vehicleCompliant,
            'vehicle_non_compliant' => $vehicleNonCompliant,
            'driver_compliant' => $driverCompliant,
            'driver_non_compliant' => $driverNonCompliant,
        ];

        return view('operations.compliance.index', compact(
            'stats',
            'expiringDocs',
            'expiredDocs',
            'vehicles',
            'drivers'
        ));
    }
}