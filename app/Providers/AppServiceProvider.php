<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\ClubPolicy;
use App\Policies\EventPolicy;
use App\Policies\UserPolicy;
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Club::class, ClubPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);

        Gate::define('manage-students', [UserPolicy::class, 'manageStudents']);
        Gate::define('manage-clubs', [ClubPolicy::class, 'manageClubLeaderAccess']);
        Gate::define('manage-announcements', [AnnouncementPolicy::class, 'create']);
        if (config('app.env') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
    }
}
