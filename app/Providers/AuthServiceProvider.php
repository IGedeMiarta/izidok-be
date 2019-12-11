<?php

namespace App\Providers;

use App\Layanan;
use App\ApiKey;
use App\Dokter;
use App\Operator;
use App\Pasien;
use App\Policies\DokterPolicy;
use App\Policies\LayananPolicy;
use App\Policies\OperatorPolicy;
use App\Policies\PasienPolicy;
use App\Policies\TransKlinikPolicy;
use App\TransKlinik;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Layanan::class => LayananPolicy::class,
        Operator::class => OperatorPolicy::class,
        Dokter::class => DokterPolicy::class,
        Pasien::class => PasienPolicy::class,
        TransKlinik::class => TransKlinikPolicy::class,
    ];

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

        Auth::viaRequest('api', function (Request $request) {
            $api_key = $request->bearerToken();
            
            if ($api_key) {
                $api_key = ApiKey::whereApiKey($api_key)
                                    ->whereDate('expired_at', '>', date('Y-m-d'))
                                    ->where('logout_at', null)
                                    ->first();
               
                if(!empty($api_key)){
                    $request->request->add(['user_id' => $api_key->user->id]);
                    return $api_key->user;
                }
            }

            return null;
        });
        
    }
}
