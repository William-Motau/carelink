<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('partner_organisation_id')->nullable()->constrained('organisations')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('booker_id')->constrained('users')->cascadeOnDelete();
            $table->enum('journey_type', ['one_way', 'return', 'scheduled', 'recurring', 'repatriation', 'other']);
            $table->enum('status', [
                'pending',
                'awaiting_partner_acceptance',
                'accepted',
                'rejected',
                'awaiting_vehicle_assignment',
                'vehicle_assigned',
                'driver_assigned',
                'confirmed',
                'dispatched',
                'en_route_to_pickup',
                'patient_onboard',
                'in_transit',
                'arrived_at_destination',
                'completed',
                'cancelled',
                'failed',
                'no_show',
            ])->default('pending');
            $table->enum('priority', ['routine', 'urgent', 'emergency'])->default('routine');

            $table->text('pickup_address');
            $table->decimal('pickup_lat', 10, 8)->nullable();
            $table->decimal('pickup_lng', 11, 8)->nullable();
            $table->string('pickup_contact_name')->nullable();
            $table->string('pickup_contact_phone')->nullable();
            $table->text('pickup_instructions')->nullable();

            $table->text('destination_address');
            $table->decimal('destination_lat', 10, 8)->nullable();
            $table->decimal('destination_lng', 11, 8)->nullable();
            $table->string('destination_contact_name')->nullable();
            $table->string('destination_contact_phone')->nullable();
            $table->text('destination_instructions')->nullable();

            $table->json('mobility_requirements')->nullable();
            $table->text('medical_notes')->nullable();
            $table->text('special_requirements')->nullable();

            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('estimated_pickup_at')->nullable();
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('actual_dropoff_at')->nullable();

            $table->decimal('fare_amount', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();

            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organisation_id', 'status', 'scheduled_at']);
            $table->index(['partner_organisation_id', 'status']);
            $table->index(['vehicle_id', 'scheduled_at']);
            $table->index(['driver_id', 'scheduled_at']);
            $table->index(['patient_id', 'status']);
            $table->index(['uuid']);
        });

        Schema::create('booking_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_history');
        Schema::dropIfExists('bookings');
    }
};