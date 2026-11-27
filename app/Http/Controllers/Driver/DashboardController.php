<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\BookingStatus;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $driver = $user->driverProfile;

        $bookings = $driver
            ? Booking::with(['patient', 'vehicle'])
                ->where('driver_id', $driver->id)
                ->whereIn('status', BookingStatus::ACTIVE)
                ->orderBy('scheduled_at')
                ->get()
            : collect();

        $completedToday = $driver
            ? Booking::where('driver_id', $driver->id)
                ->where('status', BookingStatus::COMPLETED)
                ->whereDate('actual_dropoff_at', today())
                ->count()
            : 0;

        return view('driver.dashboard', compact('driver', 'bookings', 'completedToday'));
    }
}