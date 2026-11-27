<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->enum('role', [
                'super_admin',
                'org_admin',
                'dispatch_manager',
                'finance_officer',
                'compliance_officer',
                'read_only_auditor',
                'partner_admin',
                'partner_operations_manager',
                'partner_dispatcher',
                'driver',
                'partner_finance_officer',
                'partner_read_only_auditor',
                'patient',
                'caregiver',
                'family_member',
                'booking_contact',
            ])->default('patient');
            $table->enum('status', ['pending', 'active', 'suspended', 'archived'])->default('pending');
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->string('mfa_secret')->nullable();
            $table->boolean('mfa_enabled')->default(false);
            $table->timestamp('consent_given_at')->nullable();
            $table->string('consent_version')->nullable();
            $table->timestamp('data_retention_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organisation_id', 'role']);
            $table->index(['email', 'status']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};