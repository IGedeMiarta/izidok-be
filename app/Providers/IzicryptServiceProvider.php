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
        Collection::macro('decrypt', function($arr=[], $state='only') {
            return $this->each(function($item) use($arr, $state) {
                if(empty($arr) && isset($item->encrypted) && $item->encrypted) {
                    $arr = $item->encrypted;
                }

                if($item instanceof \Illuminate\Database\Eloquent\Model) {
                    if(isset($item->encrypted_except) && $item->encrypted_except === true) {
                        $state = 'except';
                    }

                    foreach($item->getAttributes() as $key => $value) {
                        if(empty($arr)) break;
                        if(empty($value)) continue;

                        if(in_array($key, $arr) && $state=='only') {
                            $item->{$key} = Izicrypt::encrypt($value);
                        }
                        elseif(!in_array($key, $arr) && $state=='except') {
                            $item->{$key} = Izicrypt::encrypt($value);
                        }
                    }
                }
                else {
                    foreach($item as $key => $value) {
                        if(empty($arr)) break;
                        if(empty($value)) continue;

                        if(in_array($key, $arr) && $state=='only') {
                            $item->{$key} = Izicrypt::encrypt($value);
                        }
                        elseif(!in_array($key, $arr) && $state=='except') {
                            $item->{$key} = Izicrypt::encrypt($value);
                        }
                    }
                }
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
