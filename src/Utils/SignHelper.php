<?php
/**
 * Created by PhpStorm.
 * User: zfc
 * Date: 2018/4/13
 * Time: 11:40
 */

namespace JMD\Utils;

use JMD\App\Utils;

class SignHelper
{
    /**
     * @param $params
     * @param null $apiSecretKey
     * @return bool
     * @throws \Exception
     */
    public static function validateSign($params, $apiSecretKey = null)
    {
        if (!isset($params['app_key'])) {
            return false;
        }
        if (!isset($params['sign'])) {
            return false;
        }
        $sign = $params['sign'];
        unset($params['sign']);
        return $sign === self::sign($params, $apiSecretKey);
    }

    /**
     * @param $params
     * @param $apiSecretKey
     * @return string
     * @throws \Exception
     */
    public static function sign(&$params, $apiSecretKey = null)
    {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        if (empty($params['app_key'])) {
            throw new \Exception('app_key不存在');
        }
        if (!isset($params['random_str'])) {
            $params['random_str'] = self::getRandom();
        }
        if (!isset($params['time'])) {
            $params['time'] = self::getTime();
        }
        $params['secret_key'] = $apiSecretKey;
        self::ksortArray($params);
        $sign = md5(http_build_query($params));
        unset($params['secret_key']);
        return $sign;
    }

    /**
     * @param $params
     * @return mixed
     */
    public static function ksortArray(&$params)
    {
        foreach ($params as &$value) {
            if (is_array($value)) {
                self::ksortArray($value);
            }
        }
        ksort($params);
        return $params;
    }

    /**
     * 随机值
     * @return string
     */
    public static function getRandom()
    {
        return Utils::random(32);
    }

    /**
     * @return int
     */
    public static function getTime()
    {
        return time();
    }
}
