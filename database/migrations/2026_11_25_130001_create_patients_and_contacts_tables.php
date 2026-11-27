<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('id_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('mobility_requirements')->nullable();
            $table->text('medical_notes')->nullable();
            $table->json('special_requirements')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organisation_id', 'id_number']);
            $table->index(['organisation_id', 'last_name', 'first_name']);
        });

        Schema::create('patient_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('relationship');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('can_book')->default(true);
            $table->boolean('can_view_status')->default(true);
            $table->boolean('receives_notifications')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_contacts');
        Schema::dropIfExists('patients');
    }
};