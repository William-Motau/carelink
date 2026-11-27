<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journeys', function (Blueprint $table) {
            $table->id();
            $table->string('journey_reference')->unique();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->timestamp('scheduled_pickup_at')->nullable();
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('journey_start_at')->nullable();
            $table->timestamp('destination_arrival_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('journey_status', [
                'scheduled',
                'dispatched',
                'en_route_to_pickup',
                'patient_onboard',
                'in_transit',
                'arrived_at_destination',
                'completed',
                'cancelled',
                'failed',
                'no_show',
            ])->default('scheduled');
            $table->text('operational_notes')->nullable();
            $table->json('exception_records')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_id']);
            $table->index(['organisation_id', 'journey_status']);
            $table->index(['vehicle_id', 'scheduled_pickup_at']);
            $table->index(['driver_id', 'scheduled_pickup_at']);
        });

        Schema::create('journey_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_id')->constrained('journeys')->cascadeOnDelete();
            $table->string('event_type');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['journey_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_events');
        Schema::dropIfExists('journeys');
    }
};