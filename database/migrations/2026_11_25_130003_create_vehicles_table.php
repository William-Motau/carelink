<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->string('registration')->unique();
            $table->string('vin')->nullable();
            $table->string('make');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->enum('vehicle_type', ['sedan', 'suv', 'minibus', 'ambulance', 'wheelchair_van', 'stretcher_van', 'other']);
            $table->unsignedSmallInteger('capacity_seated')->default(0);
            $table->unsignedSmallInteger('capacity_wheelchair')->default(0);
            $table->unsignedSmallInteger('capacity_stretcher')->default(0);
            $table->enum('fuel_type', ['petrol', 'diesel', 'hybrid', 'electric', 'other']);
            $table->date('license_expiry')->nullable();
            $table->date('cofc_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('operating_permit_expiry')->nullable();
            $table->unsignedInteger('service_due_km')->nullable();
            $table->unsignedInteger('current_km')->default(0);
            $table->enum('status', [
                'available',
                'assigned',
                'in_service',
                'under_maintenance',
                'out_of_service',
                'compliance_suspended',
            ])->default('available');
            $table->string('tracking_device_id')->nullable();
            $table->timestamp('gps_last_ping_at')->nullable();
            $table->json('equipment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organisation_id', 'status']);
            $table->index(['registration']);
            $table->index(['license_expiry', 'cofc_expiry', 'insurance_expiry', 'operating_permit_expiry']);
        });

        Schema::create('vehicle_capabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('capability');
            $table->json('details')->nullable();
            $table->timestamps();

            $table->unique(['vehicle_id', 'capability']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_capabilities');
        Schema::dropIfExists('vehicles');
    }
};