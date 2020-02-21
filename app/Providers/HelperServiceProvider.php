<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{

    public function boot()
    {
        //
    }

    public function register()
    {
        require base_path().'/app/Helpers/EmailHelper.php';
        require base_path().'/app/Helpers/UploadFileHelper.php';
        require base_path().'/app/Helpers/ActivityLogHelper.php';
    }
}