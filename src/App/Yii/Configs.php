<?php
class Configs implements \JMD\App\Interfaces\Configs {
    public static function isProEnv()
    {
        return YII_ENV === 'prod';
    }
}