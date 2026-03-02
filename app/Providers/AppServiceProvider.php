<?php

namespace App\Providers;

use App\Models\HoaxClaim;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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
        // Share sidebar data with right-sidebar component and home page
        View::composer(['components.right-sidebar', 'home'], function ($view) {
            // Hoax Queue: open claims with approved verdict progress
            $sidebarHoaxClaims = HoaxClaim::open()
                ->withCount(['approvedVerdicts'])
                ->latest()
                ->take(3)
                ->get();

            // Trending: most-voted approved posts (by total votes) in last 30 days
            $sidebarTrending = Post::approved()
                ->withCount('votes')
                ->where('created_at', '>=', now()->subDays(30))
                ->orderByDesc('votes_count')
                ->take(5)
                ->get();

            $view->with(compact('sidebarHoaxClaims', 'sidebarTrending'));
        });
    }
}
