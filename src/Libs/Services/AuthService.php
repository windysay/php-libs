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
     * @return DataFormat
     */
    public static function authByToken($token)
    {
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        if (isset($config['auth']['api'])) {
            /** 鉴权接口 */
            $url = $config['auth']['api'];
            /** 令牌key */
            $tokenKey = $config['auth']['tokenKey'] ?? 'token';
            $params = [
                $tokenKey => $token
            ];
            $url = $url . '?' . http_build_query($params);
            return new DataFormat(HttpHelper::get($url, $params));
        }
    }

    public static function authByToken2($url, $tokenKey, $token)
    {
        /** 令牌key */
        $tokenKey = $tokenKey ?? 'token';
        $params = [
            $tokenKey => $token
        ];
        $url = $url . '?' . http_build_query($params);
        return new DataFormat(HttpHelper::get($url, $params));
    }

    /**
     * 验证身份by jwt - token
     * @param $token
     * @return DataFormat
     * @throws \Exception
     */
    public static function validateToken($token)
    {
        try {
            $parse = (new \Lcobucci\JWT\Parser())->parse($token);

            $verifyUrl = $parse->getClaim('verify_url');
            $tokenKey = $parse->getClaim('token_key');
        } catch (\OutOfBoundsException $outE) {
            throw new \Exception('verify_url or token_key does not exist in token');
        } catch (\Exception $e) {
            throw new \Exception('jwt parse error');
        }

        return self::authByToken2($verifyUrl, $tokenKey, $token);
    }
}