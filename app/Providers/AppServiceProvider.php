<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Str::macro('uuid4', function () {
            return strtolower(
                sprintf(
                    '%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0xfff),
                    bindec(substr(sprintf('%016b', mt_rand(0, 0xffff)), 1, 3)),
                    bindec(substr(sprintf('%016b', mt_rand(0, 0xffff)), 1, 3)),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff)
                )
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
