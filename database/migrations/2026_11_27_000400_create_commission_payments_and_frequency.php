<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->enum('commission_frequency', ['daily', 'weekly', 'monthly'])
                ->default('monthly')
                ->after('is_active');
        });

        Schema::create('commission_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_organisation_id')->constrained('organisations')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('ZAR');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('paypal_url')->nullable();
            $table->string('paypal_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['partner_organisation_id', 'status']);
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('commission_payment_id')->nullable()->after('id')
                ->constrained('commission_payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('commission_payment_id');
        });

        Schema::dropIfExists('commission_payments');

        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn('commission_frequency');
        });
    }
};