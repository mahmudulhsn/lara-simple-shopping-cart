<?php

namespace Mahmudulhsn\LaraSimpleShoppingCart;

use Carbon\Laravel\ServiceProvider;

class SimpleShoppingCartServiceProvider extends ServiceProvider
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
        $this->publishes([__DIR__ . "/../config/lara_simple_shopping_cart.php" => config_path("lara_simple_shopping_cart.php")], "lara-simple-shopping-cart-config");
    }
}
