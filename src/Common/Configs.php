<?php
namespace JMD\Common;

class Configs {

    static public $projectType = 'Yii';

    static public function setProjectType($projectType)
    {
        self::$projectType = $projectType;
    }
}