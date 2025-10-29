<?php

declare(strict_types=1);

namespace Modules\POS\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Order\Models\Order;

final class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'POS';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();

        // Explicit Route Model Binding for Order model
        Route::bind('order', fn (string $value) => Order::findOrFail($value));
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapDashboardRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        $webRoutePath = module_path($this->name, '/routes/web.php');
        if (file_exists($webRoutePath)) {
            Route::middleware('web')->group($webRoutePath);
        }
    }

    /**
     * Define the "dashboard" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapDashboardRoutes(): void
    {
        Route::middleware(['web', 'dashboard', 'auth:dashboard', 'dashboard.authenticated'])
            ->prefix(LaravelLocalization::setLocale() . '/dashboard')
            ->name('dashboard.')
            ->group(module_path($this->name, '/routes/dashboard.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        $apiRoutePath = module_path($this->name, '/routes/api.php');
        if (file_exists($apiRoutePath)) {
            Route::middleware('api')->prefix('api')->name('api.pos.')->group($apiRoutePath);
        }
    }
}
