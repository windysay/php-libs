<?php

namespace JMD\Common;

class Configs
{

    static public $projectType = 'Yii';

    /**
     * 自定义指定发送渠道，数据结构参考SMS类中private static $tunnels的结构
     * @var array
     */
    public static $tunnels = [];

    static public function setProjectType($projectType)
    {
        self::$projectType = $projectType;
    }

    /**
     * 允许初始化时指定发送渠道，为空时使用SMS类中private static $tunnels，不为空时使用此处$tunnels
     * @author jaylin
     */
    public static function setTunnels($tunnels)
    {
        self::$tunnels = $tunnels;
    }
}