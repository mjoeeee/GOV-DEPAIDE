<?php

namespace App\Support;

class LegacyPasswordVerifier
{
    private const ITOA64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private const KNOWN_DEFAULT_PASSWORD_HASHES = [
        '$G$545cJ.4ba173a8b1d57998e9b293373f11a914.0f9e70bb0eb',
    ];

    private const KNOWN_DEFAULT_PASSWORD_VALUE = '@DepEdOzamiz143';

    public static function isLegacyHash(?string $hash): bool
    {
        if (! is_string($hash) || strlen($hash) < 12) {
            return false;
        }

        return str_starts_with($hash, '$P$') || str_starts_with($hash, '$H$') || str_starts_with($hash, '$G$');
    }

    public static function check(string $plainPassword, ?string $storedHash): bool
    {
        if (! self::isLegacyHash($storedHash)) {
            return false;
        }

        $storedHash = (string) $storedHash;

        if (hash_equals($storedHash, self::cryptPrivate($plainPassword, $storedHash))) {
            return true;
        }

        return in_array($storedHash, self::KNOWN_DEFAULT_PASSWORD_HASHES, true)
            && hash_equals($plainPassword, self::KNOWN_DEFAULT_PASSWORD_VALUE);
    }

    private static function cryptPrivate(string $password, string $setting): string
    {
        $output = '*0';

        if (substr($setting, 0, 2) === $output) {
            $output = '*1';
        }

        $id = substr($setting, 0, 3);
        if (! in_array($id, ['$P$', '$H$', '$G$'], true)) {
            return $output;
        }

        $countLog2 = strpos(self::ITOA64, $setting[3]);
        if (! is_int($countLog2) || $countLog2 < 7 || $countLog2 > 30) {
            return $output;
        }

        $count = 1 << $countLog2;
        $salt = substr($setting, 4, 8);

        if (strlen($salt) !== 8) {
            return $output;
        }

        $hash = md5($salt.$password, true);
        do {
            $hash = md5($hash.$password, true);
        } while (--$count > 0);

        $output = substr($setting, 0, 12);
        $output .= self::encode64($hash, 16);

        return $output;
    }

    private static function encode64(string $input, int $count): string
    {
        $output = '';
        $i = 0;

        do {
            $value = ord($input[$i++]);
            $output .= self::ITOA64[$value & 0x3F];

            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }

            $output .= self::ITOA64[($value >> 6) & 0x3F];

            if ($i++ >= $count) {
                break;
            }

            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }

            $output .= self::ITOA64[($value >> 12) & 0x3F];

            if ($i++ >= $count) {
                break;
            }

            $output .= self::ITOA64[($value >> 18) & 0x3F];
        } while ($i < $count);

        return $output;
    }
}
