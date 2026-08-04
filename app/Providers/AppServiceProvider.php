<?php

namespace App\Providers;

use App\Listeners\LogAttendanceActivity;
use App\Models\Attendance;
use App\Policies\AttendancePolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        // Register attendance event subscriber
        Event::subscribe(LogAttendanceActivity::class);

        // Register attendance policy
        Gate::policy(Attendance::class, AttendancePolicy::class);
    }
}
