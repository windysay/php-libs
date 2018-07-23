<?php

namespace JMD\App\lumen;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

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
        $sendName = '系统告警',
        $sendFrom = 'auto-send@jiumiaodai.com'
    ) {
        if ($content === null) {
            $content = $title;
        }

        $flag = Mail::raw($content, function ($message) use ($sendTo, $title, $sendName, $sendFrom) {
            $message->from($sendFrom, $name = $sendName);
            $message->to($sendTo)->subject($title);
        });

        if (!$flag) {
            \Log::info('发送邮件失败！');
        }
    }

    public static function redis($dataBase = 0)
    {
        $redis = Redis::command('select', [$dataBase]);
        return $redis;
    }
}