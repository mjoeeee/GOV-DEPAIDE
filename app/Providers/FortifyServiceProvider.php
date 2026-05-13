<?php

namespace App\Providers;

use App\Actions\Fortify\LoginResponse;
use App\Models\User;
use App\Support\LegacyPasswordVerifier;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use RuntimeException;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    public function boot(): void
    {
        $this->configureViews();
        $this->configureAuthentication();
        $this->configureRateLimiting();
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn (Request $request) => Inertia::render('auth/Login', [
            'status' => $request->session()->get('status'),
        ]));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username()).'|'.$request->ip()));

            return Limit::perMinute(5)->by($throttleKey);
        });
    }

    private function configureAuthentication(): void
    {
        Fortify::authenticateUsing(function (Request $request): ?User {
            $email = Str::lower((string) $request->input('email'));
            $password = (string) $request->input('password');

            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (! $user) {
                return null;
            }

            try {
                if (Hash::check($password, (string) $user->password)) {
                    return $user;
                }
            } catch (RuntimeException) {
                // Non-bcrypt/argon hash formats are handled by legacy verifier below.
            }

            if (! LegacyPasswordVerifier::check($password, $user->password)) {
                return null;
            }

            $user->forceFill([
                'password' => $password,
            ])->save();

            return $user;
        });
    }
}
