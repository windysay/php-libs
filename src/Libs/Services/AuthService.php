<?php
/**
 * Created by PhpStorm.
 * User: Windy
 * Date: 2019/1/24
 * Time: 9:29
 */

namespace JMD\Libs\Services;


use JMD\App\Utils;
use JMD\Utils\HttpHelper;

class AuthService
{
    /**
     * 验证身份by token
     * @param $token
     * @return bool|DataFormat
     */
    public static function authByToken($token)
    {
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        if (isset($config['auth'])) {
            /** 鉴权接口 */
            $url = $config['auth']['api'];
            /** 令牌key */
            $tokenKey = $config['auth']['tokenKey'];
            $params = [
                $tokenKey => $token
            ];
            return new DataFormat(HttpHelper::get($url, $params));
        }
        return false;
    }
}