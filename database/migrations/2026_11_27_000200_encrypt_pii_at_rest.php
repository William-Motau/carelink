<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Encrypt existing plaintext medical_notes values so the new `encrypted`
     * casts can read them. Idempotent: already-encrypted values are skipped.
     */
    public function up(): void
    {
        foreach (['patients', 'bookings'] as $table) {
            $rows = DB::table($table)->whereNotNull('medical_notes')->get(['id', 'medical_notes']);

            foreach ($rows as $row) {
                $value = $row->medical_notes;

                if ($value === null || $value === '') {
                    continue;
                }

                try {
                    Crypt::decryptString($value);

                    continue; // already encrypted
                } catch (\Throwable $e) {
                    // plaintext — encrypt below
                }

                DB::table($table)
                    ->where('id', $row->id)
                    ->update(['medical_notes' => Crypt::encryptString($value)]);
            }
        }
    }

    public function down(): void
    {
        // Decrypt back to plaintext (best effort).
        foreach (['patients', 'bookings'] as $table) {
            $rows = DB::table($table)->whereNotNull('medical_notes')->get(['id', 'medical_notes']);

            foreach ($rows as $row) {
                try {
                    $plain = Crypt::decryptString($row->medical_notes);
                } catch (\Throwable $e) {
                    continue;
                }

                DB::table($table)->where('id', $row->id)->update(['medical_notes' => $plain]);
            }
        }
    }
};