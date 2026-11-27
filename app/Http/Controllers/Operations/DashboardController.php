<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organisation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $organisation = auth()->user()->organisation;
        
        // Base query scoped to organisation
        $bookingsQuery = Booking::where('organisation_id', $organisation->id);
        
        // KPIs
        $kpis = [
            'active_bookings' => (clone $bookingsQuery)->whereIn('status', \App\Support\BookingStatus::ACTIVE)->count(),
            
            'pending_acceptance' => (clone $bookingsQuery)->where('status', 'awaiting_partner_acceptance')->count(),
            
            'awaiting_dispatch' => (clone $bookingsQuery)->whereIn('status', \App\Support\BookingStatus::AWAITING_DISPATCH)->count(),
            
            'in_transit' => (clone $bookingsQuery)->whereIn('status', \App\Support\BookingStatus::IN_TRANSIT_STATUSES)->count(),
            
            'completed_today' => (clone $bookingsQuery)->where('status', 'completed')
                ->whereDate('updated_at', today())->count(),
            
            'cancelled_today' => (clone $bookingsQuery)->where('status', 'cancelled')
                ->whereDate('updated_at', today())->count(),
            
            'total_revenue_today' => (clone $bookingsQuery)->where('status', 'completed')
                ->whereDate('updated_at', today())->sum('total_amount'),
            
            'available_vehicles' => Vehicle::where('organisation_id', $organisation->id)
                ->where('status', 'available')->count(),
            
            'active_drivers' => Driver::where('organisation_id', $organisation->id)
                ->where('status', 'active')->count(),
            
            'compliance_alerts' => $this->getComplianceAlerts($organisation->id),
        ];

        // Recent bookings
        $recentBookings = $bookingsQuery->with(['patient', 'vehicle', 'driver.user'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Upcoming scheduled bookings
        $upcomingBookings = $bookingsQuery->with(['patient', 'vehicle', 'driver.user'])
            ->whereIn('status', ['pending', 'awaiting_partner_acceptance', 'accepted', 'awaiting_vehicle_assignment', 'vehicle_assigned', 'driver_assigned', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        // Vehicle status breakdown
        $vehicleStatus = Vehicle::where('organisation_id', $organisation->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Booking status breakdown
        $bookingStatus = $bookingsQuery->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Revenue trend (last 7 days)
        $revenueTrend = $bookingsQuery->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(updated_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenueData[$date] = $revenueTrend[$date]->revenue ?? 0;
        }

        return view('operations.dashboard', compact(
            'kpis',
            'recentBookings',
            'upcomingBookings',
            'vehicleStatus',
            'bookingStatus',
            'revenueData'
        ));
    }

    protected function getComplianceAlerts(int $organisationId): int
    {
        $alerts = 0;

        // Vehicle compliance: expired or expiring within 30 days
        $alerts += Vehicle::where('organisation_id', $organisationId)
            ->where(function ($q) {
                $q->where('license_expiry', '<', now())
                  ->orWhere('cofc_expiry', '<', now())
                  ->orWhere('insurance_expiry', '<', now())
                  ->orWhere('operating_permit_expiry', '<', now());
            })->count();

        $alerts += Vehicle::where('organisation_id', $organisationId)
            ->where(function ($q) {
                $q->whereBetween('license_expiry', [now(), now()->addDays(30)])
                  ->orWhereBetween('cofc_expiry', [now(), now()->addDays(30)])
                  ->orWhereBetween('insurance_expiry', [now(), now()->addDays(30)])
                  ->orWhereBetween('operating_permit_expiry', [now(), now()->addDays(30)]);
            })->count();

        // Driver compliance: expired or expiring within 30 days
        $alerts += Driver::where('organisation_id', $organisationId)
            ->where(function ($q) {
                $q->where('license_expiry', '<', now())
                  ->orWhere('prdp_expiry', '<', now())
                  ->orWhere('medical_cert_expiry', '<', now())
                  ->orWhere('background_check_expiry', '<', now());
            })->count();

        $alerts += Driver::where('organisation_id', $organisationId)
            ->where(function ($q) {
                $q->whereBetween('license_expiry', [now(), now()->addDays(30)])
                  ->orWhereBetween('prdp_expiry', [now(), now()->addDays(30)])
                  ->orWhereBetween('medical_cert_expiry', [now(), now()->addDays(30)])
                  ->orWhereBetween('background_check_expiry', [now(), now()->addDays(30)]);
            })->count();

        return $alerts;
    }
}