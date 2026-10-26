<?php

namespace App\Providers;

use App\Registers\ResourceRegister;
use Illuminate\Database\Schema\Blueprint;
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
        $registrar = new ResourceRegister($this->app['router']);

        $this->app->bind('Illuminate\Routing\ResourceRegistrar', function () use ($registrar) {
            return $registrar;
        });
        Blueprint::macro('userstamps', function () {
            /** @var Blueprint $this */

            $this->integer('created_by')
                ->nullable();

            $this->integer('updated_by')
                ->nullable();
        });
    }
}
