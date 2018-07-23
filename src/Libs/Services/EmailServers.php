<?php

namespace JMD\Libs\Services;

/**
 * 发送邮件微服务组件
 * Class EmailServers
 * @package JMD\Libs\Services
 */
class EmailServers
{
    /**
     * 发送邮件
     * @param $content
     * @param null $title
     * @param $to
     * @param $from
     * @param bool $queue | true异步发送 false同步发送
     * @return mixed
     * @throws \Exception
     */
    public static function sendEmail(
        $content,
        $title = '无主题',
        $to = 'develop-alert@jiumiaodai.com',
        $from = 'auto-send@jiumiaodai.com',
        $queue = false
    ) {
        $request = new BaseRequest();
        $url = 'email/send';
        $sendData = [
            'to' => $to,
            'form' => $from,
            'title' => $title,
            'message' => self::arrayToJson($content),
            'queue' => $queue,
        ];
        $request->setUrl($url);
        $request->setData($sendData);
        return json_decode($request->execute(), true);
    }

    /**
     * @param $body
     * @return string
     */
    private static function arrayToJson($body)
    {
        if (is_array($body)) {
            return json_encode($body, 256);
        }
        return $body;
    }
}