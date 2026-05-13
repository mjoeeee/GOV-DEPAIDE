<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LegacyPasswordLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_legacy_hash_and_password_is_upgraded(): void
    {
        $plainPassword = 'legacy-password';

        DB::table('tbl_user')->insert([
            'fullname' => 'Legacy User',
            'firstname' => 'Legacy',
            'lastname' => 'User',
            'email' => 'legacy.user@example.com',
            'password' => $this->makeLegacyHash($plainPassword, '$G$'),
            'job_title' => 'Staff',
            'role' => 'Employee',
            'remember_token' => null,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'legacy.user@example.com',
            'password' => $plainPassword,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'legacy.user@example.com')->firstOrFail();

        $this->assertTrue(Hash::check($plainPassword, $user->password));
        $this->assertStringStartsWith('$2', $user->password);
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
