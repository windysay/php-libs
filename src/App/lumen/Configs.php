<?php

namespace JMD\App\lumen;

class Configs implements \JMD\App\Interfaces\Configs
{

    public static function isProEnv()
    {
        return !env('APP_DEBUG');
    }
}