<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 18:29
 */

namespace JMD\Cache\Cookie\Sso;

use JMD\App\Utils;

class TicketCookie
{
    /**
     * @return string
     */
    private static function getKey()
    {
        return 'ticket_prod';
    }

    /**
     * 过期时间
     */
    const CACHE_EXPIRE = 43200;

    /**
     * @param $value
     * @return bool
     */
    public static function set($value)
    {
        return setcookie(self::getKey(), $value, time() + self::CACHE_EXPIRE, "/", Utils::getHost());
    }

    /**
     * @return mixed
     */
    public static function get()
    {
        return $_COOKIE[self::getKey()] ?? '';
    }

    /**
     * @return bool
     */
    public static function del()
    {
        return setcookie(self::getKey(), '', time() - self::CACHE_EXPIRE, "/", Utils::getHost());
    }
}