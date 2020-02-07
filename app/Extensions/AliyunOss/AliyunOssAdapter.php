<?php

namespace App\Extensions\AliyunOss;

use Aliyun\Flysystem\AliyunOss\AliyunOssAdapter as BaseAliyunOssAdapter;
use DateTimeInterface;
use Illuminate\Support\Carbon;

class AliyunOssAdapter extends BaseAliyunOssAdapter
{
    public function getTemporaryUrl(string $path, DateTimeInterface $expiration)
    {
        $url = $this->client->signUrl($this->bucket, $this->applyPathPrefix($path), Carbon::now()->diffInSeconds($expiration));

        $host = parse_url($url, PHP_URL_HOST);

        return str_replace($host, $this->options['custom_domain'], $url);
    }

    public function getUrl(string $path)
    {
        return strtok($this->getTemporaryUrl($path, Carbon::now()), '?');
    }
}
