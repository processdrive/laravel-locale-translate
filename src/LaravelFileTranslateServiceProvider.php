<?php
namespace ProcessDrive\LaravelFileTranslate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class LaravelFileTranslateServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        Artisan::call('migrate');
        $this->commands([
            LaravelFileTranslateDbStoreCommand::class,
            LaravelFileRetriveDbValueCommand::class,
        ]);
        $this->loadViewsFrom(__DIR__.'/views', 'LaravelFileTranslate');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }

    public function register() {  
    }
}