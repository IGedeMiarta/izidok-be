<?php

namespace App\Extensions\AliyunOss;

use Aliyun\Flysystem\AliyunOss\AliyunOssAdapter as BaseAliyunOssAdapter;
use DateTimeInterface;
use Illuminate\Support\Carbon;

class AliyunOssAdapter extends BaseAliyunOssAdapter
{
    public function getTemporaryUrl(string $path, DateTimeInterface $expiration)
    {
        return $this->client->signUrl($this->bucket, $this->applyPathPrefix($path), Carbon::now()->diffInSeconds($expiration));
    }

    public function getUrl(string $path)
    {
        return strtok($this->client->signUrl($this->bucket, $this->applyPathPrefix($path)), '?');
    }
}
