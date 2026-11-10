<?php

namespace ItmoSoft\Wishlist;

use Illuminate\Support\ServiceProvider;

class WishlistServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wishlist.php', 'wishlist');
        $this->app->singleton('wishlist', fn () => new Services\WishlistManager());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/wishlist.php' => config_path('wishlist.php'),
        ], 'wishlist-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'wishlist-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
