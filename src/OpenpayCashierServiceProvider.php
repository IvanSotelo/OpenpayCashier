<?php

namespace IvanSotelo\OpenpayCashier;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OpenpayCashierServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerLogger();
        $this->registerRoutes();
        $this->registerResources();
        $this->registerMigrations();
        $this->registerPublishing();

        // Stripe::setAppInfo(
        //     'Laravel Cashier',
        //     OpenpayCashier::VERSION,
        //     'https://laravel.com'
        // );

    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->bindLogger();
    }

    /**
     * Setup the configuration for OpenpayCashier.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/openpay-cashier.php', 'openpay-cashier'
        );
    }

    /**
     * Bind the Openpay logger interface to the OpenpayCashier logger.
     *
     * @return void
     */
    protected function bindLogger()
    {
        $this->app->bind(LoggerInterface::class, function ($app) {
            return new Logger(
                $app->make('log')->channel(config('openpay-cashier.logger'))
            );
        });
    }

    /**
     * Register the Openpay logger.
     *
     * @return void
     */
    protected function registerLogger()
    {
        if (config('openpay-cashier.logger')) {
            //Stripe::setLogger($this->app->make(LoggerInterface::class));
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if (OpenpayCashier::$registersRoutes) {
            Route::group([
                'prefix' => config('openpay-cashier.path'),
                'namespace' => 'IvanSotelo\OpenpayCashier\Http\Controllers',
                'as' => 'openpay-cashier.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });
        }
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'openpay-cashier');
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (OpenpayCashier::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/openpay-cashier.php' => $this->app->configPath('openpay-cashier.php'),
            ], 'openpay-cashier-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'openpay-cashier-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/openpay-cashier'),
            ], 'openpay-cashier-views');
        }
    }
}