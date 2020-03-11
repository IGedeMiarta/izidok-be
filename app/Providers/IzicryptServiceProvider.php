<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection;
use App\Izicrypt\Facade\Izicrypt;

class IzicryptServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('decrypt', function($encrypted=[], $state='only') {
            return $this->each(function($item) use($encrypted, $state) {
                Izicrypt::itemCollectionDecrypt($item, $encrypted, $state);
            });
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('izicrypt', function() {
            return new \App\Izicrypt\Izicrypt;
        });
    }
}
