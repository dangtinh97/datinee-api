<?php

namespace App\Providers;

use App\Helpers\StrHelper;
use Illuminate\Auth\Events\Validated;
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
        app('validator')->extend('object_id', function ($attribute, $value) {
            return StrHelper::isObjectId($value);
        });
    }
}
