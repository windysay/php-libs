<?php

namespace JMD\App\ci;

class Utils implements \JMD\App\Interfaces\Utils
{
    private static $redis;

    public static function getParam($key)
    {
        return get_instance()->config->item($key);
    }

    public static function getCache($key)
    {
        return self::initCache()->get($key);
    }

    public static function setCache($key, $value, $duration = null)
    {
        self::initCache()->set($key, $value, $duration);
    }

    public static function logError($log)
    {
        log_message('error', $log);
    }

    public static function alert(
        $title,
        $content = null,
        $sendTo = 'develop-alert@jiumiaodai.com',
        $sendName = '系统告警',
        $sendFrom = 'auto-send@jiumiaodai.com'
    ) {
        log_message('error', $content);
        return true;
    }

    public static function redis($dataBase = 1)
    {
        $redis = self::initCache();
        $redis->select($dataBase);
        return $redis;
    }

    private static function initCache()
    {
        $instance = get_instance();
        $instance->load->driver('cache');
        return $instance->cache->redis;
    }
}