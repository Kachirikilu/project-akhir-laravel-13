<?php

namespace App\Providers;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsDosen;
use App\Http\Middleware\IsMahasiswa;
use App\Http\Middleware\IsStaff;
use Carbon\CarbonImmutable;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local' || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            \URL::forceScheme('https');
        }
        $this->configureDefaults();

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('is_admin', IsAdmin::class);
        $router->aliasMiddleware('is_dosen', IsDosen::class);
        $router->aliasMiddleware('is_staff', IsStaff::class);
        $router->aliasMiddleware('is_mahasiswa', IsMahasiswa::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
