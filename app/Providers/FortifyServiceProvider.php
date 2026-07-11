<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegisterResponse;
use App\Http\Responses\TwoFactorLoginResponse;
use App\Models\Auth\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
// use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(TwoFactorLoginResponseContract::class, TwoFactorLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();

        // Fortify::loginRequest(function (Request $request) {
        //     return LoginRequest::createFrom($request, [
        //         'email' => ['required', 'string'],
        //         'password' => ['required', 'string'],
        //     ], [
        //         'email.failed' => 'Email atau ID Akademik dan Password yang Anda masukkan tidak cocok dengan Sistem kami!',
        //     ]);
        // });

        Fortify::authenticateUsing(function (Request $request) {
            $identifier = $request->input('email');
            $password = $request->password;

            $user = User::where('email', $identifier)->first();
            if (! $user) {
                $user = User::whereHas('admin', function ($query) use ($identifier) {
                    $query->where('nip', $identifier);
                })->first();
            }
            if (! $user) {
                $user = User::whereHas('dosen', function ($query) use ($identifier) {
                    $query->where('nip', $identifier);
                })->first();
            }
            if (! $user) {
                $user = User::whereHas('mahasiswa', function ($query) use ($identifier) {
                    $query->where('nim', $identifier);
                })->first();
            }

            // Verifikasi password
            if ($user && Hash::check($password, $user->password)) {
                return $user;
            }
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });
        Route::middleware(['web', 'check.registration'])->group(function () {});
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages::auth.login'));
        // Fortify::verifyEmailView(fn () => view('pages::auth.verify-email'));
        // Fortify::twoFactorChallengeView(fn () => view('pages::auth.two-factor-challenge'));
        // Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));
        Fortify::registerView(fn () => view('pages::auth.register'));
        // Fortify::resetPasswordView(fn () => view('pages::auth.reset-password'));
        // Fortify::requestPasswordResetLinkView(fn () => view('pages::auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
