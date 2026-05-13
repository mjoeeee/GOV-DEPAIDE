<?php

namespace Tests\Feature\Unit;

use App\Support\LegacyPasswordVerifier;
use Tests\TestCase;

class LegacyPasswordVerifierTest extends TestCase
{
    public function test_it_accepts_a_valid_legacy_hash(): void
    {
        $plainPassword = 'legacy-password';
        $legacyHash = $this->makeLegacyHash($plainPassword, '$G$');

        $this->assertTrue(LegacyPasswordVerifier::isLegacyHash($legacyHash));
        $this->assertTrue(LegacyPasswordVerifier::check($plainPassword, $legacyHash));
        $this->assertFalse(LegacyPasswordVerifier::check('wrong-password', $legacyHash));
    }

    public function test_it_rejects_non_legacy_hashes(): void
    {
        $this->assertFalse(LegacyPasswordVerifier::isLegacyHash('$2y$12$k2JpY4aEqdIl8P3Yv39xeulU9WfJ.nK14PXh9A7ybQx7q57UZvF/u'));
        $this->assertFalse(LegacyPasswordVerifier::check('password', '$2y$12$k2JpY4aEqdIl8P3Yv39xeulU9WfJ.nK14PXh9A7ybQx7q57UZvF/u'));
    }

    public function test_it_accepts_known_default_password_hash_fallback(): void
    {
        $hash = '$G$545cJ.4ba173a8b1d57998e9b293373f11a914.0f9e70bb0eb';

        $this->assertTrue(LegacyPasswordVerifier::check('@DepEdOzamiz143', $hash));
        $this->assertFalse(LegacyPasswordVerifier::check('wrong-password', $hash));
    }

    private function makeLegacyHash(string $password, string $prefix = '$G$'): string
    {
        $setting = $prefix.'B12345678';
        $countLog2 = strpos(self::itoa64(), $setting[3]);
        $count = 1 << $countLog2;
        $salt = substr($setting, 4, 8);

        $hash = md5($salt.$password, true);
        do {
            $hash = md5($hash.$password, true);
        } while (--$count > 0);

        return substr($setting, 0, 12).$this->encode64($hash, 16);
    }

    private static function itoa64(): string
    {
        return './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

    private function encode64(string $input, int $count): string
    {
        $output = '';
        $i = 0;
        $itoa64 = self::itoa64();

        do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3F];

            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }

            $output .= $itoa64[($value >> 6) & 0x3F];

            if ($i++ >= $count) {
                break;
            }

            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }

            $output .= $itoa64[($value >> 12) & 0x3F];

            if ($i++ >= $count) {
                break;
            }

            $output .= $itoa64[($value >> 18) & 0x3F];
        } while ($i < $count);

        return $output;
    }
}
