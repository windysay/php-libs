<?php

namespace JMD;

use JMD\Common\Configs;

class JMD
{
    static function init($options = [])
    {
        if (isset($options['projectType'])) {
            Configs::setProjectType($options['projectType']);
        }
        if (isset($options['tunnels'])) {
            Configs::setTunnels($options['tunnels']);
        }
    }
}