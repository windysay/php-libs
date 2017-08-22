<?php
namespace JMD\App\Interfaces;

interface Utils {
    public static function getCache($key);

    public static function setCache($key, $value, $duration = null);

    public static function logError($log);

    public static function alert($title, $content = null);
}