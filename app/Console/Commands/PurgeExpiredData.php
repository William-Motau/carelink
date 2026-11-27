<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'carelink:purge-expired-data';

    /**
     * The console command description.
     */
    protected $description = 'Remove expired compliance documents, anonymise users past their retention date, and clear stale password reset tokens (POPIA data minimisation).';

    public function handle(): int
    {
        $documents = Document::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($documents as $document) {
            Storage::disk('local')->delete($document->file_path);
            $document->delete();
        }

        // Anonymise users whose POPIA retention period has elapsed.
        $anonymised = User::query()
            ->whereNotNull('data_retention_until')
            ->where('data_retention_until', '<', now())
            ->where('status', '!=', 'archived')
            ->get();

        foreach ($anonymised as $user) {
            $user->forceFill([
                'first_name' => 'Deleted',
                'last_name' => 'User',
                'email' => 'anonymised-' . $user->id . '@example.invalid',
                'phone' => null,
                'status' => 'archived',
                'data_retention_until' => null,
            ])->save();
        }

        $resetTokens = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subDay())
            ->delete();

        $this->info(sprintf(
            'Purged %d expired document(s), anonymised %d user(s), and %d stale password reset token(s).',
            $documents->count(),
            $anonymised->count(),
            $resetTokens
        ));

        return self::SUCCESS;
    }
}