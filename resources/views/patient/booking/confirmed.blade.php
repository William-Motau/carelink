@extends('layouts.guest')

@section('title', 'Booking Confirmed - CareLink')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>
            <p class="text-gray-600 mb-6">Your patient transport booking has been submitted successfully.</p>
            
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-6 mb-6">
                <div class="text-4xl font-bold text-primary-600 mb-1">{{ $booking->reference }}</div>
                <div class="text-gray-600">Your Booking Reference</div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-6">
                <a href="{{ route('booking.status', ['uuid' => $booking->uuid]) }}" class="btn-primary px-6 py-3">
                    Track Booking Status
                </a>
                <button onclick="copyReference()" class="btn-secondary px-6 py-3" type="button">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy Reference
                </button>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Booking Details</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-3">Patient</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Name:</dt>
                            <dd class="font-medium">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Contact:</dt>
                            <dd class="font-medium">{{ $booking->patient->phone }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-900 mb-3">Journey</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Type:</dt>
                            <dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $booking->journey_type)) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Status:</dt>
                            <dd class="font-medium">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                       ($booking->status === 'awaiting_partner_acceptance' ? 'bg-blue-100 text-blue-800' :
                                       ($booking->status === 'accepted' ? 'bg-green-100 text-green-800' :
                                       ($booking->status === 'dispatched' ? 'bg-purple-100 text-purple-800' :
                                       'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Scheduled:</dt>
                            <dd class="font-medium">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y \a\t H:i') : 'Not scheduled' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Pickup:</dt>
                            <dd class="font-medium text-right text-sm">{{ $booking->pickup_address }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Destination:</dt>
                            <dd class="font-medium text-right text-sm">{{ $booking->destination_address }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Fare Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Fare Summary</h2>
            
            <dl class="space-y-2">
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-600">Estimated Fare:</dt>
                    <dd class="font-medium">R{{ number_format($booking->fare_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt class="text-gray-600">VAT (15%):</dt>
                    <dd class="font-medium">R{{ number_format($booking->vat_amount, 2) }}</dd>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between">
                    <dt class="font-semibold text-gray-900">Total Estimated:</dt>
                    <dd class="font-semibold text-lg text-primary-600">R{{ number_format($booking->total_amount, 2) }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-xs text-gray-500">Final fare may vary based on actual distance travelled and any additional services provided. Payment is due after journey completion.</p>
        </div>

        <!-- What Happens Next -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">What Happens Next?</h2>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <span class="text-sm font-bold text-primary-600">1</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Booking Review</h3>
                        <p class="text-gray-600 text-sm mt-1">Our operations team will review your booking and match it with an available vehicle and crew.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <span class="text-sm font-bold text-primary-600">2</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Confirmation</h3>
                        <p class="text-gray-600 text-sm mt-1">You will receive SMS and/or email confirmation once a vehicle and crew are assigned.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <span class="text-sm font-bold text-primary-600">3</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Journey Day</h3>
                        <p class="text-gray-600 text-sm mt-1">The crew will contact the pickup contact 30 minutes before arrival. Track progress using your booking reference.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <span class="text-sm font-bold text-primary-600">4</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Completion</h3>
                        <p class="text-gray-600 text-sm mt-1">After the journey, you'll receive a summary and invoice. Payment can be made via your preferred method.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-8">
            <h3 class="font-semibold text-amber-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Important Reminders
            </h3>
            <ul class="text-sm text-amber-800 space-y-1">
                <li>• Keep your booking reference <strong>{{ $booking->reference }}</strong> for all communications</li>
                <li>• Ensure the pickup contact is available at the scheduled time</li>
                <li>• Have the patient's medical documents and ID ready for the crew</li>
                <li>• Cancellation policy: Free cancellation up to 2 hours before scheduled pickup</li>
                <li>• For urgent changes, call our 24/7 operations line: <strong>0800 227 354</strong></li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('booking.status', ['uuid' => $booking->uuid]) }}" class="btn-primary px-6 py-3 text-center">
                Track Live Status
            </a>
            <a href="/" class="btn-secondary px-6 py-3 text-center">
                Book Another Transport
            </a>
        </div>
    </div>

    <script>
        function copyReference() {
            const reference = '{{ $booking->reference }}';
            navigator.clipboard.writeText(reference).then(function() {
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>Copied!';
                btn.classList.add('bg-green-100', 'text-green-700', 'border-green-300');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-100', 'text-green-700', 'border-green-300');
                }, 2000);
            });
        }
    </script>
</div>
@endsection