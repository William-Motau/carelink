<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('partner_organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('adjustments', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('partner_payable', 12, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->foreignId('pricing_rule_version_id')->nullable()->constrained('pricing_rule_versions')->nullOnDelete();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->unique(['booking_id']);
            $table->index(['partner_organisation_id', 'calculated_at']);
        });

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->foreignId('organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->foreignId('partner_organisation_id')->nullable()->constrained('organisations')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('settlement_id')->nullable()->constrained('settlements')->nullOnDelete();
            $table->enum('transaction_type', ['booking_payment', 'commission', 'settlement_payment', 'adjustment', 'refund', 'vat']);
            $table->enum('direction', ['credit', 'debit']);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->decimal('balance_after', 12, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transacted_at')->useCurrent();
            $table->timestamps();

            $table->index(['organisation_id', 'transacted_at']);
            $table->index(['partner_organisation_id', 'transacted_at']);
            $table->index(['booking_id']);
            $table->index(['settlement_id']);
        });

        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('total_vat', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'paid', 'partially_paid', 'disputed', 'cancelled'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->string('csv_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['partner_organisation_id', 'period_start', 'period_end']);
            $table->index(['partner_organisation_id', 'status']);
        });

        Schema::create('settlement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->constrained('settlements')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('commission_id')->constrained('commissions')->cascadeOnDelete();
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('vat_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->timestamps();

            $table->index(['settlement_id']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_items');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('commissions');
    }
};