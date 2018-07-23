<?php

namespace JMD\App;

class BaseClass
{
    public static $class;
    public static $instance = [];

    public static function __callStatic($name, $arguments)
    {
        $class = static::$class;
        if (!isset(self::$instance[$class])) {
            $project_type = \JMD\Common\Configs::$projectType;
            self::$instance[$class] = "\\JMD\\App\\{$project_type}\\{$class}";
        }
        return call_user_func_array([self::$instance[$class], $name], $arguments);
    }
}