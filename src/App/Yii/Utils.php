<?php

namespace JMD\App\Yii;

use JMD\App\Configs;
use Yii;

class Utils implements \JMD\App\Interfaces\Utils
{
    public static function getParam($key)
    {
        return Yii::$app->params[$key];
    }

    public static function getCache($key)
    {
        return Yii::$app->cache->get($key);
    }

    public static function setCache($key, $value, $duration = null)
    {
        Yii::$app->cache->set($key, $value, $duration);
    }

    public static function logError($log)
    {
        Yii::error($log);
    }

    public static function alert(
        $title,
        $content = null,
        $sendTo = 'develop-alert@jiumiaodai.com',
        $sendName = '系统告警',
        $sendFrom = 'auto-send@jiumiaodai.com'
    ) {
//        self::logError($title . ':' . $content);
        if ($content === null) {
            $content = $title;
        }
        // 生产环境发送
        return Configs::isProEnv()
            ? Yii::$app->mailer->compose()
                ->setFrom([$sendFrom => $sendName])
                ->setTo($sendTo)
                ->setSubject($title)
                ->setHtmlBody($content)
                ->send()
            : true;
    }

    public static function redis($dataBase = 0)
    {
        $redis = Yii::$app->redis;
        $redis->executeCommand('select', [$dataBase]);
        $redis->database = $dataBase;
        return $redis;
    }
}