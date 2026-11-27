<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_number')->unique();
            $table->string('license_number')->unique();
            $table->string('license_class');
            $table->date('license_expiry');
            $table->date('prdp_expiry')->nullable();
            $table->date('medical_cert_expiry')->nullable();
            $table->date('background_check_expiry')->nullable();
            $table->json('training_certificates')->nullable();
            $table->enum('status', ['active', 'suspended', 'expired_docs', 'on_leave'])->default('active');
            $table->json('availability_schedule')->nullable();
            $table->foreignId('current_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organisation_id', 'status']);
            $table->index(['license_expiry', 'prdp_expiry', 'medical_cert_expiry', 'background_check_expiry']);
            $table->index(['current_vehicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};