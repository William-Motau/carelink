<?php

namespace App\Services;

use App\Models\ConsentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConsentService
{
    public const VERSION = '1.0';

    /**
     * Record that a user granted (or declined) a consent.
     */
    public function record(
        int $userId,
        string $consentType,
        bool $granted,
        ?string $version = null,
        array $metadata = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): ConsentRecord {
        return ConsentRecord::create([
            'user_id' => $userId,
            'consent_type' => $consentType,
            'version' => $version ?? self::VERSION,
            'granted' => $granted,
            'metadata' => $metadata,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'granted_at' => $granted ? now() : null,
        ]);
    }

    /**
     * Withdraw a previously granted consent.
     */
    public function withdraw(int $userId, string $consentType, ?string $version = null): ConsentRecord
    {
        return ConsentRecord::create([
            'user_id' => $userId,
            'consent_type' => $consentType,
            'version' => $version ?? self::VERSION,
            'granted' => false,
            'withdrawn_at' => now(),
        ]);
    }

    /**
     * Whether the user currently has an active (granted, not withdrawn) consent.
     */
    public function hasConsent(int $userId, string $consentType, ?string $version = null): bool
    {
        $latest = ConsentRecord::where('user_id', $userId)
            ->where('consent_type', $consentType)
            ->when($version, fn ($q) => $q->where('version', $version))
            ->latest('id')
            ->first();

        return $latest?->granted === true && $latest->withdrawn_at === null;
    }

    /**
     * Record a batch of consents for a user, updating the user's consent summary.
     *
     * @param  array<string,bool>  $consents  e.g. ['privacy' => true, 'marketing' => false]
     */
    public function recordBatch(
        int $userId,
        array $consents,
        ?string $version = null,
        array $metadata = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void {
        DB::transaction(function () use ($userId, $consents, $version, $metadata, $ipAddress, $userAgent) {
            foreach ($consents as $type => $granted) {
                $this->record($userId, (string) $type, (bool) $granted, $version, $metadata, $ipAddress, $userAgent);
            }

            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'consent_given_at' => now(),
                    'consent_version' => $version ?? self::VERSION,
                ]);
            }
        });
    }
}