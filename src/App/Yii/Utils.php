<?php

namespace JMD\App\Yii;

use JMD\JMD;
use JMD\Libs\Services\EmailServers;
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
        $sendName = 'php-lib告警',
        $sendFrom = 'auto-send@jiumiaodai.com'
    ) {
        if ($content === null) {
            $content = $title;
        }

        /** 接入邮件微服务start */
        try {
            JMD::init(['projectType' => 'Yii']);
            $res = EmailServers::sendEmail($content, $title, $sendTo, $sendFrom);
            if ($res->isSuccess()) {
                return true;
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
        /** 接入邮件微服务end */

        return Yii::$app->mailer->compose()
                ->setFrom([$sendFrom => $sendName])
                ->setTo($sendTo)
                ->setSubject($title)
                ->setHtmlBody($content)
            ->send();
    }

    public static function redis($dataBase = 0)
    {
        $redis = Yii::$app->redis;
        $redis->executeCommand('select', [$dataBase]);
        $redis->database = $dataBase;
        return $redis;
    }
}