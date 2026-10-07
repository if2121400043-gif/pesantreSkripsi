<?php

namespace App\Providers;

use App\Listeners\LogAttendanceActivity;
use App\Models\Attendance;
use App\Policies\AttendancePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Force HTTPS when behind a reverse proxy (e.g. Heroku)
        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // Register attendance event subscriber
        Event::subscribe(LogAttendanceActivity::class);

        // Register attendance policy
        Gate::policy(Attendance::class, AttendancePolicy::class);
    }
}
