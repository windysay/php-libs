<?php

namespace JMD\Libs\Services;

use JMD\App\Configs;

/**
 *
 * Class SsoService
 * @package JMD\Libs\Services
 */
class SsoService
{
    public static $url = 'http://sms-chengxs.dev23.jiumiaodai.com/';

    /**
     * 鉴权
     * @param $mobile
     * @param $code
     * @param $app_name
     * @return DataFormat
     * @throws \Exception
     */
    public static function httpSsoCheck($ticket, $ip, $actionId)
    {
        $request = new BaseRequest();
        $url = 'oa/api/sso/token';
        $request->setUrl($url);

        $host = $_SERVER['HTTP_HOST'];
        $post_data = [
            'ticket' => $ticket,
            'ip' => $ip,
            'Referer' => $host,
            'route' => $actionId,
        ];

        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

}