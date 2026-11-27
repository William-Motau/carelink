<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laravel's default database notifications only populate type/data/read_at.
     * The custom schema made title/body NOT NULL, which breaks default
     * notifications. Relax them to nullable.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->text('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->text('title')->nullable(false)->change();
            $table->text('body')->nullable(false)->change();
        });
    }
};