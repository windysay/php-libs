<?php

namespace JMD\App\lumen;

class Configs implements \JMD\App\Interfaces\Configs
{

    public static function isProEnv()
    {
        return App::environment() === 'production';
    }
}