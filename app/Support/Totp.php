<?php

namespace App\Support;

/**
 * Minimal RFC 6238 TOTP implementation (SHA1, 6 digits, 30s step).
 * Avoids adding a third-party dependency for second-factor auth.
 */
class Totp
{
    private const PERIOD = 30;

    private const DIGITS = 6;

    /**
     * Generate a base32-encoded random secret.
     */
    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    /**
     * Compute the current (or given timestamp) TOTP code for a secret.
     */
    public static function code(string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $counter = intdiv($timestamp, self::PERIOD);

        return self::hotp($secret, $counter);
    }

    /**
     * Verify a user-supplied code against the secret, allowing +/- 1 window.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\D+/', '', $code);

        if (strlen($code) !== self::DIGITS) {
            return false;
        }

        $timestamp = time();

        for ($i = -$window; $i <= $window; $i++) {
            $counter = intdiv($timestamp, self::PERIOD) + $i;

            if (hash_equals(self::hotp($secret, $counter), $code)) {
                return true;
            }
        }

        return false;
    }

    private static function hotp(string $secret, int $counter): string
    {
        $key = self::base32Decode($secret);
        $binCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binCounter, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $part = substr($hash, $offset, 4);
        $value = unpack('N', $part)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % (10 ** self::DIGITS)), self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($data) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $result = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0');
            $result .= $alphabet[bindec($chunk)];
        }

        return $result;
    }

    private static function base32Decode(string $base32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32 = strtoupper(rtrim($base32, '='));
        $bits = '';

        foreach (str_split($base32) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $data = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $data .= chr(bindec($chunk));
            }
        }

        return $data;
    }
}