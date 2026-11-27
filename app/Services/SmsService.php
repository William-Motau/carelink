<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message to the given phone number.
     *
     * Returns true when the message was accepted (or logged in the log driver).
     * Returns false when SMS is disabled or no recipient is available.
     */
    public function send(?string $to, string $message): bool
    {
        if (! config('carelink.notifications.sms_enabled')) {
            return false;
        }

        $phone = $this->normalize($to);
        if (! $phone || ! $message) {
            return false;
        }

        $driver = config('carelink.notifications.sms_driver', 'log');

        return match ($driver) {
            'clickatell' => $this->sendViaClickatell($phone, $message),
            default => $this->log($phone, $message),
        };
    }

    /**
     * Normalize a local (South African) phone number to E.164 format.
     */
    public function normalize(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (! $digits) {
            return null;
        }

        // Local format 08XXXXXXXX -> 278XXXXXXXX
        if (str_starts_with($digits, '0')) {
            $digits = '27' . substr($digits, 1);
        } elseif (! str_starts_with($digits, '27')) {
            $digits = '27' . $digits;
        }

        return '+' . $digits;
    }

    protected function log(string $phone, string $message): bool
    {
        Log::info('[SMS] to '.$this->mask($phone).': '.$this->preview($message));

        return true;
    }

    /**
     * Mask a phone number for logging, e.g. +27821234567 -> +27****4567.
     */
    protected function mask(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) < 6) {
            return '***';
        }

        $prefix = substr($digits, 0, 3);
        $suffix = substr($digits, -4);

        return $prefix.str_repeat('*', max(0, strlen($digits) - 7)).$suffix;
    }

    /**
     * Truncate the message body so full PII is not written to the log.
     */
    protected function preview(string $message): string
    {
        $message = trim($message);

        return strlen($message) > 40 ? substr($message, 0, 40).'…' : $message;
    }

    protected function sendViaClickatell(string $phone, string $message): bool
    {
        $apiKey = config('services.clickatell.api_key');
        if (! $apiKey) {
            Log::warning('[SMS] Clickatell API key not configured; falling back to log.');

            return $this->log($phone, $message);
        }

        $response = Http::withHeaders(['Authorization' => 'Bearer '.$apiKey])
            ->acceptJson()
            ->post('https://api.clickatell.com/rest/message', [
                'content' => $message,
                'to' => [$phone],
                'from' => config('services.clickatell.from'),
            ]);

        if ($response->successful()) {
            return true;
        }

        Log::error('[SMS] Clickatell send failed: status '.$response->status());

        return false;
    }
}