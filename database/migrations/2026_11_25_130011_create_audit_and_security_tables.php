<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('event');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['event', 'created_at']);
        });

        Schema::create('data_subject_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_type');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->text('description')->nullable();
            $table->enum('status', ['submitted', 'in_progress', 'completed', 'rejected', 'withdrawn'])->default('submitted');
            $table->json('requested_data')->nullable();
            $table->json('response_data')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['email', 'status']);
        });

        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_type');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->text('description');
            $table->json('affected_data')->nullable();
            $table->json('affected_users')->nullable();
            $table->enum('status', ['detected', 'investigating', 'contained', 'resolved', 'closed'])->default('detected');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('contained_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
            $table->index(['detected_at']);
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('security_incidents');
        Schema::dropIfExists('data_subject_requests');
        Schema::dropIfExists('audit_logs');
    }
};