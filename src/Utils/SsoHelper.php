<?php

namespace JMD\Utils;

use JMD\App\Utils;
use JMD\Cache\Cookie\Sso\TicketCookie;
use JMD\Libs\Services\BaseRequest;

class SsoHelper
{
    const LOGIN_STATUS_SUCCESS = 200;//已登录，成功登录
    const LOGIN_STATUS_FAIL = 1001;//未登录，已退出，过期

    /**
     * @param $ticket
     * @return mixed
     */
    public static function getTicket($ticket)
    {
        //如果有get的ticket，获取并写入cookie
        if ($ticket) {
            if(strstr('+', $ticket)) {
                $ticket = str_replace('+', '%2B', urlencode($ticket));
            }
            TicketCookie::set($ticket);
            return $ticket;
        }
        //否则读cookie的ticket
        $ticket = TicketCookie::get();
        return $ticket;
    }

    /**
     * 统一登录失败返回
     *
     * @param string $msg
     * @return array
     */
    public static function resFail($msg = '')
    {
        return [
            'code' => self::LOGIN_STATUS_FAIL,
            'sso_login_url' => self::getSsoLoginUrl(),
            'sso_logout_url' => self::getSsoLogoutUrl(),
            'msg' => $msg,
        ];
    }

    /**
     * 获取登录地址
     *
     * @return string
     */
    public static function getSsoLoginUrl()
    {
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        return $config['sso_endpoint'] . 'sso/login';
    }

    /**
     * 获取退出地址
     *
     * @return string
     */
    public static function getSsoLogoutUrl()
    {
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        return $config['sso_endpoint'] . 'sso/logout';
    }
}