<?php
namespace ProcessDrive\LaravelFileTranslate;

use Illuminate\Support\ServiceProvider;


class LaravelFileTranslateServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->commands([
            LaravelFileTranslateDbStoreCommand::class,
            LaravelFileRetriveDbValueCommand::class,
        ]);
        $this->loadViewsFrom(__DIR__.'/views', 'LaravelFileTranslate');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        \Artisan::call('migrate');
        \Artisan::call('translate:filetodb');
        \Artisan::call('translate:dbtofile');
    }

    public function register() {  
    }
}
