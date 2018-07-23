<?php

namespace JMD\App\Yii;

class Configs implements \JMD\App\Interfaces\Configs
{
    public static function isProEnv()
    {
        return YII_ENV === 'prod';
    }
}