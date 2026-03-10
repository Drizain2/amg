<?php

namespace App\Providers;

use App\Models\Branche;
use App\Models\Compagnie;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Observers\StockMovementObserver;
use App\Policies\BranchePolicy;
use App\Policies\CompagniePolicy;
use App\Policies\ProductPolicy;
use App\Policies\StockMovementPolicy;
use App\Policies\UserPolicy;
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
        StockMovement::observe(StockMovementObserver::class);

        // ─── Policies ────────────────────────────────────────────
        Gate::policy(Compagnie::class, CompagniePolicy::class);
        Gate::policy(Branche::class, BranchePolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(StockMovement::class, StockMovementPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
