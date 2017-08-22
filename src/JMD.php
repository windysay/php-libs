<?php
namespace JMD;

use JMD\Common\Configs;
class JMD {
    static function init($options = [])
    {
        if (isset($options['projectType'])) {
            Configs::setProjectType($options['projectType']);
        }
    }
}