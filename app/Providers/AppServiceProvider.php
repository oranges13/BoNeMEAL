<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale(config('app.locale'));
        setlocale(LC_ALL, config('app.locale'));

        if (config('database.default') === 'sqlite') {
            $path = config('database.connections.sqlite.database');
            if (!file_exists($path) && is_dir(dirname($path))) {
                touch($path);
            }
        }
    }
}
