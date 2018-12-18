<?php

namespace JMD\App\lumen;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use JMD\JMD;
use JMD\Libs\Services\EmailServers;

class Utils implements \JMD\App\Interfaces\Utils
{

    public static function getParam($key)
    {
        return config('domain.' . $key, '');
    }

    public static function getCache($key)
    {
        return Redis::get($key);
    }

    public static function setCache($key, $value, $duration = null)
    {
        return Redis::set($key, $value);
    }

    public static function logError($log)
    {
        \Log::error($log);
    }

    public static function alert(
        $title,
        $content = null,
        $sendTo = 'develop-alert@jiumiaodai.com',
        $sendName = 'php-lib告警',
        $sendFrom = 'auto-send@jiumiaodai.com'
    ) {
        if ($content === null) {
            $content = $title;
        }

        /** 接入邮件微服务start */
        try {
            JMD::init(['projectType' => 'lumen']);
            $res = EmailServers::sendEmail($content, $title, $sendTo, $sendFrom);
            if ($res->isSuccess()) {
                return true;
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
        /** 接入邮件微服务end */

        Mail::raw($content, function ($message) use ($sendTo, $title, $sendName, $sendFrom) {
            $message->from($sendFrom, $name = $sendName);
            $message->to($sendTo)->subject($title);
        });
    }

    public static function redis($dataBase = '')
    {
        if($dataBase != ''){
            $redis = Redis::command('select', [$dataBase]);
        }
        return app('redis');
        //return $redis;
    }
}