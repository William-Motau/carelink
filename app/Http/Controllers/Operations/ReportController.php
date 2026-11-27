<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index()
    {
        $organisation = auth()->user()->organisation;

        $stats = [
            'total' => Booking::where('organisation_id', $organisation->id)->count(),
            'completed' => Booking::where('organisation_id', $organisation->id)->where('status', 'completed')->count(),
            'cancelled' => Booking::where('organisation_id', $organisation->id)->where('status', 'cancelled')->count(),
            'revenue' => Booking::where('organisation_id', $organisation->id)->where('status', 'completed')->sum('total_amount'),
        ];

        return view('operations.reports.index', compact('stats'));
    }

    public function export(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $bookings = Booking::where('organisation_id', $organisation->id)
            ->with('patient')
            ->latest()
            ->get();

        $headers = ['Reference', 'Patient', 'Status', 'Scheduled At', 'Total (ZAR)'];

        $rows = $bookings->map(fn (Booking $b) => [
            $b->reference,
            $b->patient?->full_name ?? '',
            $b->status,
            optional($b->scheduled_at)->format('Y-m-d H:i') ?? '',
            number_format((float) $b->total_amount, 2, '.', ''),
        ]);

        $csv = $this->toCsv($headers, $rows);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-report.csv"',
        ]);
    }

    /**
     * Build a CSV string, neutralising spreadsheet formula injection.
     */
    private function toCsv(array $headers, $rows): string
    {
        $guard = static function ($value) {
            $value = (string) $value;
            if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                return "'" . $value;
            }

            return $value;
        };

        $lines = [];

        $lines[] = implode(',', array_map($guard, $headers));

        foreach ($rows as $row) {
            $lines[] = implode(',', array_map($guard, $row));
        }

        return implode("\n", $lines);
    }
}