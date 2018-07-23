<?php

namespace JMD\App\ci;

class Configs implements \JMD\App\Interfaces\Configs
{

    public static function isProEnv()
    {
        return IS_PRODUCTION_ENV;
    }
}