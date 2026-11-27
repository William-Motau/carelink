<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('rule_type', ['base_fare', 'distance', 'additional_passenger', 'special_vehicle', 'additional_stop', 'waiting', 'surcharge', 'manual_adjustment']);
            $table->decimal('value', 12, 2);
            $table->enum('value_type', ['fixed', 'percentage', 'per_km', 'per_minute', 'per_passenger']);
            $table->json('conditions')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->timestamps();

            $table->index(['organisation_id', 'rule_type', 'is_active']);
        });

        Schema::create('pricing_rule_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_rule_id')->constrained('pricing_rules')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('rule_snapshot');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['pricing_rule_id', 'version']);
        });

        Schema::create('fare_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->json('calculation_details');
            $table->decimal('base_fare', 12, 2)->default(0);
            $table->decimal('distance_charge', 12, 2)->default(0);
            $table->decimal('additional_passenger_charge', 12, 2)->default(0);
            $table->decimal('special_vehicle_charge', 12, 2)->default(0);
            $table->decimal('additional_stop_charge', 12, 2)->default(0);
            $table->decimal('waiting_charge', 12, 2)->default(0);
            $table->decimal('surcharges', 12, 2)->default(0);
            $table->decimal('manual_adjustments', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('pricing_rule_version')->nullable();
            $table->foreignId('calculated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fare_calculations');
        Schema::dropIfExists('pricing_rule_versions');
        Schema::dropIfExists('pricing_rules');
    }
};