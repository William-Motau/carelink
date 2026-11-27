<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Organisation;
use App\Models\Patient;
use App\Models\PatientContact;
use App\Models\PricingRule;
use App\Models\User;
use App\Services\ConsentService;
use App\Services\FareCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class BookingController extends Controller
{
    protected array $steps = [
        1 => 'contact',
        2 => 'patient',
        3 => 'journey',
        4 => 'requirements',
        5 => 'review',
        6 => 'submit',
    ];

    protected array $stepTitles = [
        1 => 'Booking Contact',
        2 => 'Patient Information',
        3 => 'Journey Details',
        4 => 'Requirements',
        5 => 'Review & Consent',
        6 => 'Submit',
    ];

    public function create()
    {
        session()->forget('booking_wizard');
        return redirect()->route('booking.step.show', ['step' => 1]);
    }

    public function showStep(int $step)
    {
        if ($step < 1 || $step > 6) {
            return redirect()->route('booking.step.show', ['step' => 1]);
        }

        $wizardData = session('booking_wizard', []);
        // The review step (5) summarises the entire wizard, so it needs all data.
        $currentStepData = $step === 5 ? $wizardData : ($wizardData[$this->steps[$step]] ?? []);

        // Check if previous steps are complete
        for ($i = 1; $i < $step; $i++) {
            if (empty($wizardData[$this->steps[$i]])) {
                return redirect()->route('booking.step.show', ['step' => $i])
                    ->with('error', 'Please complete the previous steps first.');
            }
        }

        $journeyTypes = [
            'one_way' => 'One Way',
            'return' => 'Return',
            'scheduled' => 'Scheduled',
            'recurring' => 'Recurring',
            'repatriation' => 'Repatriation',
            'other' => 'Other',
        ];

        $mobilityOptions = [
            'wheelchair' => 'Wheelchair Accessible',
            'stretcher' => 'Stretcher Transport',
            'walking_aid' => 'Walking Aid Required',
            'oxygen' => 'Oxygen Required',
            'escort_required' => 'Medical Escort Required',
            'bariatric' => 'Bariatric Equipment Needed',
            'other' => 'Other',
        ];

        $relationships = [
            'self' => 'Myself (I am the patient)',
            'family' => 'Family Member',
            'caregiver' => 'Caregiver',
            'other' => 'Other Authorized Representative',
        ];

        $stepTitles = $this->stepTitles;

        return view('patient.booking.step-' . $step, compact(
            'step',
            'stepTitles',
            'currentStepData',
            'journeyTypes',
            'mobilityOptions',
            'relationships'
        ));
    }

    public function storeStep(Request $request, int $step)
    {
        $rules = $this->getStepRules($step);
        $messages = $this->getStepMessages($step);
        
        $validated = $request->validate($rules, $messages);

        $wizardData = session('booking_wizard', []);
        // Store this step's data. The validated array is keyed by the form
        // prefix (e.g. ['contact' => [...]]), so unwrap it to avoid nesting
        // the data under the same key twice.
        $stepKey = $this->steps[$step];
        $wizardData[$stepKey] = $validated[$stepKey] ?? $validated;
        
        // Calculate fare estimate for review step
        if ($step >= 3) {
            $wizardData['fare_estimate'] = $this->calculateFareEstimate($wizardData);
        }

        session(['booking_wizard' => $wizardData]);

        if ($step < 6) {
            return redirect()->route('booking.step.show', ['step' => $step + 1])
                ->with('success', 'Step ' . $step . ' completed. Continuing to step ' . ($step + 1) . '.');
        }

        return redirect()->route('booking.step.show', ['step' => 6]);
    }

    public function submit(Request $request)
    {
        $wizardData = $this->normalizeWizardData(session('booking_wizard', []));

        if (empty($wizardData)) {
            return redirect()->route('booking.step.show', ['step' => 1])
                ->with('error', 'Booking session expired. Please start over.');
        }

        // Verify idempotency key
        $idempotencyKey = $request->header('Idempotency-Key') ?? $request->input('idempotency_key');

        // Check reCAPTCHA
        if (!$this->verifyRecaptcha($request->input('g_recaptcha_response'))) {
            return back()->withErrors(['g_recaptcha_response' => 'Please complete the security verification.']);
        }

        // Re-validate the pickup time at final submission: the wizard may have
        // been open for a while and the originally selected slot could now be
        // in the past.
        $scheduledAt = $wizardData['journey']['scheduled_at'] ?? null;
        if ($scheduledAt && strtotime((string) $scheduledAt) <= time()) {
            return redirect()->route('booking.step.show', ['step' => 3])
                ->with('error', 'The selected pickup time has passed. Please choose a new time.');
        }

        // When required, reject bookings that omit coordinates (which would
        // otherwise fall back to a fixed default distance and under/overcharge).
        if (config('carelink.fare.require_coordinates')) {
            $j = $wizardData['journey'] ?? [];
            $missingCoords = array_filter([
                'pickup_lat' => $j['pickup_lat'] ?? null,
                'pickup_lng' => $j['pickup_lng'] ?? null,
                'destination_lat' => $j['destination_lat'] ?? null,
                'destination_lng' => $j['destination_lng'] ?? null,
            ], fn ($v) => $v === null || $v === '');

            if (! empty($missingCoords)) {
                return redirect()->route('booking.step.show', ['step' => 3])
                    ->with('error', 'Pickup and destination coordinates are required to calculate the fare.');
            }
        }

        // Recompute the fare authoritatively on the server. The session/client
        // estimate is never trusted for the persisted amounts.
        $organisationId = $this->regiaOrganisationId();
        $journey = $wizardData['journey'] ?? [];
        $fare = app(FareCalculatorService::class)->calculate([
            'pickup_lat' => $journey['pickup_lat'] ?? null,
            'pickup_lng' => $journey['pickup_lng'] ?? null,
            'destination_lat' => $journey['destination_lat'] ?? null,
            'destination_lng' => $journey['destination_lng'] ?? null,
            'mobility_requirements' => $wizardData['requirements']['mobility'] ?? [],
            'journey_type' => $journey['journey_type'] ?? 'one_way',
            'scheduled_at' => $scheduledAt ?? now(),
            'organisation_id' => $organisationId,
        ]);
        $commissionRate = (float) config('carelink.default_commission_rate', 0);

        // Serialise duplicate submissions that share an idempotency key.
        $lock = $idempotencyKey ? Cache::lock('booking:idem:'.$idempotencyKey, 10) : null;
        if ($lock && ! $lock->get()) {
            return back()->with('info', 'Your booking is already being processed. Please wait a moment.');
        }

        try {
            // Re-check inside the lock so concurrent duplicates cannot both create.
            if ($idempotencyKey && ($existingBooking = $this->findByIdempotencyKey($idempotencyKey))) {
                return redirect()->route('booking.confirmed', ['uuid' => $existingBooking->uuid])
                    ->with('info', 'This booking was already submitted.');
            }

            return DB::transaction(function () use ($wizardData, $idempotencyKey, $request, $fare, $commissionRate, $organisationId) {
                // Create or find patient
                $patient = $this->createOrFindPatient($wizardData);
                
                // Create booking contact if different from patient
                $contact = $this->createBookingContact($wizardData, $patient);

                // Create booking
                $booking = Booking::create([
                    'uuid' => Str::uuid(),
                    'organisation_id' => $organisationId,
                    'patient_id' => $patient->id,
                    'booker_id' => $contact->user_id ?? $patient->user_id,
                    'journey_type' => $wizardData['journey']['journey_type'] ?? 'one_way',
                    'status' => 'pending',
                    'priority' => $this->determinePriority($wizardData),
                    
                    // Pickup
                    'pickup_address' => $wizardData['journey']['pickup_address'],
                    'pickup_lat' => $wizardData['journey']['pickup_lat'] ?? null,
                    'pickup_lng' => $wizardData['journey']['pickup_lng'] ?? null,
                    'pickup_contact_name' => $wizardData['journey']['pickup_contact_name'],
                    'pickup_contact_phone' => $wizardData['journey']['pickup_contact_phone'],
                    'pickup_instructions' => $wizardData['journey']['pickup_instructions'] ?? null,
                    
                    // Destination
                    'destination_address' => $wizardData['journey']['destination_address'],
                    'destination_lat' => $wizardData['journey']['destination_lat'] ?? null,
                    'destination_lng' => $wizardData['journey']['destination_lng'] ?? null,
                    'destination_contact_name' => $wizardData['journey']['destination_contact_name'],
                    'destination_contact_phone' => $wizardData['journey']['destination_contact_phone'],
                    'destination_instructions' => $wizardData['journey']['destination_instructions'] ?? null,
                    
                    // Requirements
                    'mobility_requirements' => $wizardData['requirements']['mobility'] ?? [],
                    'medical_notes' => $wizardData['requirements']['medical_notes'] ?? null,
                    'special_requirements' => $wizardData['requirements']['special'] ?? null,
                    
                    // Scheduling
                    'requested_at' => now(),
                    'scheduled_at' => $wizardData['journey']['scheduled_at'] ?? null,
                    'estimated_pickup_at' => $wizardData['journey']['scheduled_at'] ?? null,
                    
                    // Financial (authoritatively recalculated by FareCalculatorService)
                    'fare_amount' => $fare['subtotal'],
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $this->calculateCommission($fare['subtotal'], $commissionRate),
                    'vat_amount' => $fare['vat_amount'],
                    'total_amount' => $fare['total_amount'],
                    'payment_status' => 'unpaid',
                    
                    // Metadata
                    'metadata' => [
                        'idempotency_key' => $idempotencyKey,
                        'booking_contact' => $wizardData['contact'],
                        'consents' => $wizardData['review']['consents'] ?? [],
                    ],
                ]);

                // Record status history
                $booking->statusHistory()->create([
                    'previous_status' => null,
                    'new_status' => 'pending',
                    'actor_id' => $booking->booker_id,
                    'organisation_id' => $booking->organisation_id,
                    'reason' => 'Booking submitted by patient/caregiver',
                ]);

                // Record POPIA consent audit trail
                $consents = $wizardData['review']['consents'] ?? [];
                if (! empty($consents) && $booking->booker_id) {
                    app(ConsentService::class)->recordBatch(
                        (int) $booking->booker_id,
                        $consents,
                        null,
                        ['booking_reference' => $booking->reference, 'source' => 'booking_wizard'],
                        $request->ip(),
                        $request->userAgent(),
                    );
                }

                // Clear wizard session
                session()->forget('booking_wizard');

                // Trigger notifications
                $this->sendBookingNotifications($booking);

                return redirect()->route('booking.confirmed', ['uuid' => $booking->uuid]);
            });
        } catch (\Exception $e) {
            Log::error('Booking submission failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'An error occurred while submitting your booking. Please try again.')
                ->withInput();
        } finally {
            $lock?->release();
        }
    }

    public function confirmed(string $uuid)
    {
        $booking = Booking::with(['patient', 'organisation'])->where('uuid', $uuid)->firstOrFail();
        
        return view('patient.booking.confirmed', compact('booking'));
    }

    public function status(string $uuid)
    {
        $booking = Booking::with([
            'patient', 
            'vehicle', 
            'driver.user', 
            'journey', 
            'statusHistory.actor',
            'organisation'
        ])->where('uuid', $uuid)->firstOrFail();

        // Unauthenticated trackers (e.g. via QR link) see only minimal status;
        // full contact/PII details require an authenticated owner/operator.
        $canViewFull = $this->canViewBookingFull($booking);

        return view('patient.booking.status', compact('booking', 'canViewFull'));
    }

    /**
     * Whether the current user may see full booking PII (owner or same-org operator).
     */
    protected function canViewBookingFull(Booking $booking): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($booking->booker_id === $user->id || $booking->patient?->user_id === $user->id) {
            return true;
        }

        return $user->isAdmin()
            && $user->organisation_id !== null
            && $user->organisation_id === $booking->organisation_id;
    }

    public function cancel(Request $request, string $uuid)
    {
        $booking = Booking::where('uuid', $uuid)->firstOrFail();

        $this->authorizeBookingOwner($booking);
        // Check if booking can be cancelled (more than 2 hours before scheduled pickup)
        if ($booking->scheduled_at && $booking->scheduled_at->diffInHours(now()) < 2) {
            return back()->with('error', 'Bookings can only be cancelled more than 2 hours before the scheduled pickup time.');
        }
        
        if (!in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted', 'awaiting_vehicle_assignment'])) {
            return back()->with('error', 'This booking cannot be cancelled at its current status.');
        }
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id() ?? $booking->booker_id,
            'cancellation_reason' => $request->input('reason'),
        ]);
        
        $booking->statusHistory()->create([
            'previous_status' => $booking->getOriginal('status'),
            'new_status' => 'cancelled',
            'actor_id' => auth()->id() ?? $booking->booker_id,
            'organisation_id' => $booking->organisation_id,
            'reason' => $request->input('reason'),
        ]);
        
        return redirect()->route('booking.status', ['uuid' => $uuid])
            ->with('success', 'Booking has been cancelled.');
    }

    public function reschedule(Request $request, string $uuid)
    {
        $booking = Booking::where('uuid', $uuid)->firstOrFail();

        $this->authorizeBookingOwner($booking);
        if (!in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted'])) {
            return back()->with('error', 'This booking cannot be rescheduled at its current status.');
        }
        
        $request->validate([
            'new_scheduled_at' => 'required|date|after:now',
        ]);
        
        $booking->update([
            'scheduled_at' => $request->input('new_scheduled_at'),
            'estimated_pickup_at' => $request->input('new_scheduled_at'),
        ]);

        // Recalculate the fare authoritatively for the new schedule.
        app(FareCalculatorService::class)->calculateForBooking($booking);
        
        $booking->statusHistory()->create([
            'previous_status' => $booking->status,
            'new_status' => $booking->status,
            'actor_id' => auth()->id() ?? $booking->booker_id,
            'organisation_id' => $booking->organisation_id,
            'reason' => 'Booking rescheduled to ' . $request->input('new_scheduled_at'),
            'metadata' => ['new_scheduled_at' => $request->input('new_scheduled_at')],
        ]);
        
        return redirect()->route('booking.status', ['uuid' => $uuid])
            ->with('success', 'Booking has been rescheduled.');
    }

    /**
     * Ensure the current user owns the booking (or is an operator admin).
     */
    protected function authorizeBookingOwner(Booking $booking): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Please sign in to manage this booking.');
        }

        $isOwner = $booking->booker_id === $user->id
            || $booking->patient?->user_id === $user->id;

        // Operators may only act within their own organisation.
        $isOrgAdmin = $user->isAdmin()
            && $user->organisation_id !== null
            && $user->organisation_id === $booking->organisation_id;

        if (! $isOwner && ! $isOrgAdmin) {
            abort(403, 'You are not allowed to modify this booking.');
        }
    }

    protected function getStepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'contact.role' => ['required', 'in:self,family,caregiver,other'],
                'contact.first_name' => ['required', 'string', 'max:100'],
                'contact.last_name' => ['required', 'string', 'max:100'],
                'contact.phone' => ['required', 'string', 'regex:/^(\+27|0)[0-9]{9}$/'],
                'contact.email' => ['required', 'email', 'max:255'],
                'contact.relationship' => ['required', 'string', 'max:100'],
            ],
            2 => [
                'patient.first_name' => ['required', 'string', 'max:100'],
                'patient.last_name' => ['required', 'string', 'max:100'],
                'patient.id_number' => ['nullable', 'string', 'size:13'],
                'patient.date_of_birth' => ['nullable', 'date', 'before:today'],
                'patient.phone' => ['nullable', 'string', 'regex:/^(\+27|0)[0-9]{9}$/'],
                'patient.email' => ['nullable', 'email', 'max:255'],
            ],
            3 => [
                'journey.pickup_address' => ['required', 'string', 'max:500'],
                'journey.pickup_lat' => ['nullable', 'numeric', 'between:-90,90'],
                'journey.pickup_lng' => ['nullable', 'numeric', 'between:-180,180'],
                'journey.pickup_contact_name' => ['required', 'string', 'max:100'],
                'journey.pickup_contact_phone' => ['required', 'string', 'regex:/^(\+27|0)[0-9]{9}$/'],
                'journey.pickup_instructions' => ['nullable', 'string', 'max:500'],
                'journey.destination_address' => ['required', 'string', 'max:500'],
                'journey.destination_lat' => ['nullable', 'numeric', 'between:-90,90'],
                'journey.destination_lng' => ['nullable', 'numeric', 'between:-180,180'],
                'journey.destination_contact_name' => ['required', 'string', 'max:100'],
                'journey.destination_contact_phone' => ['required', 'string', 'regex:/^(\+27|0)[0-9]{9}$/'],
                'journey.destination_instructions' => ['nullable', 'string', 'max:500'],
                'journey.journey_type' => ['required', 'in:one_way,return,scheduled,recurring,repatriation,other'],
                'journey.scheduled_at' => ['required', 'date', 'after:now'],
                'journey.return_required' => ['nullable', 'boolean'],
                'journey.return_scheduled_at' => ['nullable', 'date', 'after:journey.scheduled_at'],
            ],
            4 => [
                'requirements.mobility' => ['required', 'array', 'min:1'],
                'requirements.mobility.*' => ['in:wheelchair,stretcher,walking_aid,oxygen,escort_required,bariatric,other'],
                'requirements.medical_notes' => ['nullable', 'string', 'max:1000'],
                'requirements.special' => ['nullable', 'string', 'max:500'],
            ],
            5 => [
                'review.consents.privacy' => ['required', 'accepted'],
                'review.consents.personal_info' => ['required', 'accepted'],
                'review.consents.terms' => ['required', 'accepted'],
                'review.consents.marketing' => ['nullable', 'boolean'],
                'g_recaptcha_response' => ['nullable', 'string'],
            ],
            6 => [
                'idempotency_key' => ['required', 'string', 'max:100'],
            ],
            default => [],
        };
    }

    protected function getStepMessages(int $step): array
    {
        return match ($step) {
            1 => [
                'contact.role.required' => 'Please select your relationship to the patient.',
                'contact.phone.regex' => 'Please enter a valid South African phone number (e.g., 082 123 4567).',
            ],
            2 => [
                'patient.id_number.size' => 'South African ID number must be 13 digits.',
                'patient.date_of_birth.before' => 'Date of birth must be in the past.',
            ],
            3 => [
                'journey.pickup_address.required' => 'Pickup address is required.',
                'journey.destination_address.required' => 'Destination address is required.',
                'journey.scheduled_at.after' => 'Scheduled date/time must be in the future.',
                'journey.return_scheduled_at.after' => 'Return date/time must be after the pickup date/time.',
            ],
            4 => [
                'requirements.mobility.min' => 'Please select at least one mobility requirement.',
            ],
            5 => [
                'review.consents.privacy.accepted' => 'You must accept the privacy notice to proceed.',
                'review.consents.personal_info.accepted' => 'You must consent to processing of personal information.',
                'review.consents.terms.accepted' => 'You must accept the terms and conditions.',
            ],
        };
    }

    protected function calculateFareEstimate(array $wizardData): array
    {
        $journey = $wizardData['journey'] ?? [];
        $requirements = $wizardData['requirements']['mobility'] ?? [];

        return app(FareCalculatorService::class)->calculate([
            'pickup_lat' => isset($journey['pickup_lat']) ? (float) $journey['pickup_lat'] : null,
            'pickup_lng' => isset($journey['pickup_lng']) ? (float) $journey['pickup_lng'] : null,
            'destination_lat' => isset($journey['destination_lat']) ? (float) $journey['destination_lat'] : null,
            'destination_lng' => isset($journey['destination_lng']) ? (float) $journey['destination_lng'] : null,
            'mobility_requirements' => $requirements,
            'journey_type' => $journey['journey_type'] ?? 'one_way',
            'scheduled_at' => $journey['scheduled_at'] ?? now(),
            'organisation_id' => config('carelink.regia_org_id'),
        ]);
    }

    protected function determinePriority(array $wizardData): string
    {
        $journeyType = $wizardData['journey']['journey_type'] ?? 'one_way';
        $requirements = $wizardData['requirements']['mobility'] ?? [];
        
        if ($journeyType === 'repatriation' || in_array('stretcher', $requirements)) {
            return 'emergency';
        }
        
        if ($journeyType === 'scheduled' || in_array('wheelchair', $requirements)) {
            return 'urgent';
        }
        
        return 'routine';
    }

    /**
     * Commission amount = subtotal * rate (rate is a percentage, e.g. 10.00).
     */
    protected function calculateCommission($subtotal, float $rate): float
    {
        return round(((float) $subtotal) * ($rate / 100), 2);
    }

    protected function createOrFindPatient(array $wizardData): Patient
    {
        $patientData = $wizardData['patient'] ?? [];
        $contactData = $wizardData['contact'] ?? [];
        
        // If booking for self, use contact info
        if (($contactData['role'] ?? 'self') === 'self') {
            $patientData = array_merge($patientData, [
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'],
                'phone' => $contactData['phone'],
                'email' => $contactData['email'],
            ]);
        }
        
        // Try to find an existing patient by ID number, but ONLY reuse the
        // record when the submitter proves ownership (DOB / phone / email
        // match). Matching on ID number alone allows patient-record takeover.
        if (!empty($patientData['id_number'])) {
            $candidate = Patient::where('id_number', $patientData['id_number'])->first();
            if ($candidate && $this->patientOwnershipMatches($candidate, $patientData)) {
                return $candidate;
            }
        }
        
        // Create new patient
        $patient = Patient::create([
            'organisation_id' => $this->regiaOrganisationId(),
            'first_name' => $patientData['first_name'],
            'last_name' => $patientData['last_name'],
            'id_number' => $patientData['id_number'] ?? null,
            'date_of_birth' => $patientData['date_of_birth'] ?? null,
            'phone' => $patientData['phone'] ?? null,
            'email' => $patientData['email'] ?? null,
            'mobility_requirements' => $wizardData['requirements']['mobility'] ?? [],
            'medical_notes' => $wizardData['requirements']['medical_notes'] ?? null,
            'special_requirements' => $wizardData['requirements']['special'] ?? null,
        ]);
        
        return $patient;
    }

    /**
     * Verify the submitter actually owns an existing patient record before
     * reusing it. Requires at least one of: matching date of birth, phone, or
     * email. ID number alone is not sufficient (it is not secret).
     */
    protected function patientOwnershipMatches(Patient $patient, array $data): bool
    {
        if (! empty($data['date_of_birth']) && $patient->date_of_birth) {
            $dob = \Carbon\Carbon::parse($data['date_of_birth'])->toDateString();
            if ($patient->date_of_birth->toDateString() === $dob) {
                return true;
            }
        }

        if (! empty($data['phone']) && ! empty($patient->phone)) {
            if (preg_replace('/\D+/', '', (string) $patient->phone) === preg_replace('/\D+/', '', (string) $data['phone'])) {
                return true;
            }
        }

        if (! empty($data['email']) && ! empty($patient->email)) {
            if (strcasecmp((string) $patient->email, (string) $data['email']) === 0) {
                return true;
            }
        }

        return false;
    }

    protected function createBookingContact(array $wizardData, Patient $patient): PatientContact
    {
        $contactData = $wizardData['contact'] ?? [];
        $patientData = $wizardData['patient'] ?? [];
        
        // If booking for self, link to the patient's user. Only bind a user to
        // a freshly-created patient; never re-point an existing patient record
        // (which would allow account/record takeover).
        if (($contactData['role'] ?? 'self') === 'self') {
            $userId = $patient->user_id ?? $this->resolveOrCreateUser($contactData, 'patient');
            if ($patient->wasRecentlyCreated && ! $patient->user_id && $userId) {
                $patient->update(['user_id' => $userId]);
            }

            return PatientContact::create([
                'patient_id' => $patient->id,
                'user_id' => $userId,
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'],
                'relationship' => 'self',
                'phone' => $contactData['phone'],
                'email' => $contactData['email'],
                'is_primary' => true,
                'can_book' => true,
                'can_view_status' => true,
                'receives_notifications' => true,
            ]);
        }
        
        // Create new contact
        return PatientContact::create([
            'patient_id' => $patient->id,
            'user_id' => $this->resolveOrCreateUser($contactData, 'booking_contact'),
            'first_name' => $contactData['first_name'],
            'last_name' => $contactData['last_name'],
            'relationship' => $contactData['relationship'],
            'phone' => $contactData['phone'],
            'email' => $contactData['email'],
            'is_primary' => false,
            'can_book' => true,
            'can_view_status' => true,
            'receives_notifications' => true,
        ]);
    }

    /**
     * Unwrap legacy double-nested wizard data (e.g. contact.contact) so the
     * submit path works with sessions created before the storeStep fix.
     */
    protected function normalizeWizardData(array $wizardData): array
    {
        foreach (['contact', 'patient', 'journey', 'requirements', 'review'] as $key) {
            if (isset($wizardData[$key][$key]) && is_array($wizardData[$key][$key])) {
                $wizardData[$key] = $wizardData[$key][$key];
            }
        }

        return $wizardData;
    }

    /**
     * Resolve the Regia Mobility organisation id from config or the database.
     */
    protected function regiaOrganisationId(): ?int
    {
        $id = config('carelink.organisations.regia_mobility.id');

        return $id ? (int) $id : Organisation::where('slug', 'regia-mobility')->value('id');
    }

    /**
     * Resolve the acting user id for a booking's booker. Authenticated users
     * are linked to their own account. Guests get a fresh, synthetic account:
     * the submitter-supplied email is NEVER used to create or link an account
     * (that would allow account pre-claiming / linking to a victim's account).
     */
    protected function resolveOrCreateUser(array $data, string $role): int
    {
        if (auth()->check()) {
            return auth()->id();
        }

        $user = User::create([
            'first_name' => $data['first_name'] ?? 'Guest',
            'last_name' => $data['last_name'] ?? 'User',
            'email' => 'guest-' . Str::lower(Str::random(12)) . '@carelink.local',
            'password' => Hash::make(Str::random(24)),
            'role' => $role,
            'status' => 'active',
        ]);

        return $user->id;
    }

    protected function verifyRecaptcha(?string $response): bool
    {
        // Disabled only when explicitly turned off (e.g. local/dev, automated
        // tests). Never keyed off APP_ENV, which is dangerous if misconfigured
        // in production.
        if (! config('services.recaptcha.enabled', false)) {
            return true;
        }

        $secret = config('services.recaptcha.secret_key');

        if (! $secret || ! $response) {
            return false;
        }

        try {
            $result = Http::asForm()
                ->timeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => request()->ip(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification request failed: '.$e->getMessage());

            return false;
        }

        if (! $result->successful()) {
            return false;
        }

        return (bool) ($result->json('success') ?? false);
    }

    /**
     * Look up an existing booking by its idempotency key.
     *
     * Uses a JSON path query for speed, but falls back to PHP-side filtering
     * because SQLite's json_extract() throws "malformed JSON" if any row has
     * empty or invalid metadata.
     */
    protected function findByIdempotencyKey(string $key): ?Booking
    {
        try {
            return Booking::query()->where('metadata->idempotency_key', $key)->first();
        } catch (\Throwable $e) {
            return Booking::query()
                ->whereNotNull('metadata')
                ->get()
                ->first(static function (Booking $booking) use ($key) {
                    return is_array($booking->metadata)
                        && ($booking->metadata['idempotency_key'] ?? null) === $key;
                });
        }
    }

    protected function sendBookingNotifications(Booking $booking): void
    {
        // Queue notifications
        // $booking->patient->contacts->each(function ($contact) use ($booking) {
        //     $contact->notify(new BookingSubmittedNotification($booking));
        // });
        
        // Notify operations team
        // Notification::send($operationsUsers, new NewBookingNotification($booking));
    }
}