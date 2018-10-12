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
     * @param string|array $content
     * @param string $title
     * @param string|array $to
     * @param string $from
     * @param string|array $attachments
     * @return DataFormat
     * @throws \Exception
     */
    public static function sendEmail(
        $content,
        $title = '无主题',
        $to = 'develop-alert@jiumiaodai.com',
        $from = 'auto-send@jiumiaodai.com',
        $attachments = ''
    ) {
        $request = new BaseRequest();
        $url = 'api/email/send';
        $sendData = [
            'to' => $to,
            'form' => $from,
            'title' => $title,
            'message' => $content,
            'attachments' => $attachments,
        ];
        $request->setUrl($url);
        $request->setData($sendData);
        return $request->execute();
    }
}