<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        // Registrar la política para el modelo Product
        Gate::policy(Product::class, ProductPolicy::class);

        // Definir el Gate 'manage-products'
        Gate::define('manage-products', function ($user) {
            return $user->role === 'admin';
        });
    }
}
