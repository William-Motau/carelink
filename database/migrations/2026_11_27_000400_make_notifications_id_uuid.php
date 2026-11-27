<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laravel's default DatabaseNotification uses a uuid primary key. The custom
     * schema created notifications.id as an auto-incrementing bigint, which
     * breaks default database notifications. Convert it to a uuid.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->uuid('id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
    }
};