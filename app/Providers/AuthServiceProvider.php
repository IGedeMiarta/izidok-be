<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        // $this->app['auth']->viaRequest('api', function ($request) {
        //     if ($request->input('api_token')) {
        //         return User::where('api_token', $request->input('api_token'))->first();
        //     }
        // });

        Auth::viaRequest('auth', function (Request $request) {
            $api_key = $request->bearerToken();

            if ($api_key) {
                $api_key = ApiKey::whereApiKey($api_key)
                                            ->whereDate('expired_at', '>', date('Y-m-d'))
                                            ->first();

                $user = $api_key->user;

                if(!empty($user)){
                    $request->request->add(['user_id' => $user->id]);
                }
                
                    return $user;
            }
        });
    }
}
