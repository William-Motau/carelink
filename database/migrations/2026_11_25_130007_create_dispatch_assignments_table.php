<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->enum('status', ['assigned', 'acknowledged', 'released', 'completed'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['vehicle_id', 'assigned_at']);
            $table->index(['driver_id', 'assigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_assignments');
    }
};