<?php

namespace JMD\App\Lumen;

class Configs implements \JMD\App\Interfaces\Configs
{

    public static function isProEnv()
    {
        return !env('APP_DEBUG');
    }
}