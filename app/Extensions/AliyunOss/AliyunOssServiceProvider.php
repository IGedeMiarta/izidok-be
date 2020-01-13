<?php

namespace App\Extensions\AliyunOss;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use League\Flysystem\Filesystem;
use OSS\OssClient;

class AliyunOssServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('oss', function (Application $app, array $config) {
            $client = new OssClient($config['key'], $config['secret'], $config['url']);

            return new Filesystem(new AliyunOssAdapter($client, $config['bucket'], $config['root']));
        });
    }
}
