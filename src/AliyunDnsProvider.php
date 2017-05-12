<?php

namespace Ykaej\Aliyun;

use Illuminate\Support\ServiceProvider;

class AliyunDnsProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('aliyun_dns',function (){
            return new DNSDomain();
        });//app('aliyun_dns')
    }
}
