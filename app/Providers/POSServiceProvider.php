<?php

declare(strict_types=1);

namespace Modules\POS\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\POS\Contracts\CashMovementContract;
use Modules\POS\Contracts\PrinterContract;
use Modules\POS\Contracts\ShiftContract;
use Modules\POS\Contracts\TerminalContract;
use Modules\POS\Http\Middleware\POSMenuMiddleware;
use Modules\POS\Models\CashMovement;
use Modules\POS\Models\Printer;
use Modules\POS\Models\Shift;
use Modules\POS\Models\Terminal;
use Modules\POS\Repositories\CashMovementRepository;
use Modules\POS\Repositories\PrinterRepository;
use Modules\POS\Repositories\ShiftRepository;
use Modules\POS\Repositories\TerminalRepository;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class POSServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'POS';

    protected string $nameLower = 'pos';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerPolymorphicRelations();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        // Register middleware after the application is fully booted
        $this->app->booted(function (): void {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('dashboard', POSMenuMiddleware::class);
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // EventServiceProvider disabled - events registered in main app EventServiceProvider
        // $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Register Contract bindings
        $this->registerRepositories();
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'resources/lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'resources/lang'));
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);

        $sourcePath = module_path($this->name, 'resources/views');

        // Only register views if the views directory exists
        if (is_dir($sourcePath)) {
            $this->publishes([
                $sourcePath => $viewPath,
            ], ['views', $this->nameLower . '-module-views']);

            $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Register the Polymorphic relations for the POS models.
     */
    public function registerPolymorphicRelations(): void
    {
        Relation::enforceMorphMap([
            'pos_terminal' => Terminal::class,
            'pos_shift' => Shift::class,
            'pos_cash_movement' => CashMovement::class,
            'pos_printer' => Printer::class,
        ]);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && 'php' === $file->getExtension()) {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = ('config.php' === $relativePath) ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register repository bindings.
     */
    private function registerRepositories(): void
    {
        $this->app->bind(TerminalContract::class, TerminalRepository::class);
        $this->app->bind(ShiftContract::class, ShiftRepository::class);
        $this->app->bind(CashMovementContract::class, CashMovementRepository::class);
        $this->app->bind(PrinterContract::class, PrinterRepository::class);
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
