<?php
namespace JMD\Libs\Sms;

use JMD\App\Utils;
use JMD\Libs\Sms\Tunnels\JPush;
use JMD\Libs\Sms\Tunnels\XuanWu;

/**
 * Class CaptchaSender
 * 验证码发送类
 * @package JMD\Libs\Sms
 */
class CaptchaSender
{
    /**
     * 短信验证码sdk数组
     *
     * @var array
     */
    private static $tunnels = [
        XuanWu::class, //玄武
        JPush::class // 极光
    ];
    private static $mobile;


    /**
     * 发送短信验证码
     * @param string|integer $mobile 手机号
     * @param null $captcha 验证码（选填）
     * @return bool|string 成功返回验证码失败返回false
     */
    public static function send($mobile, $captcha = null)
    {
        if (!self::setMobile($mobile)) {
            return false;
        }

        return self::sendCaptcha($captcha);
    }

    /**
     * 设置手机号码，若非法手机号则设置失败
     * @param $mobile
     * @return bool
     */
    private static function setMobile($mobile)
    {
        // 校验手机号码
        $mobile = intval($mobile);
        if(!preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            return false;
        }
        self::$mobile = $mobile;
        return true;
    }

    private static function sendCaptcha($captcha)
    {
        $mobile = self::$mobile;
        $captcha = self::getRandomCaptcha($captcha);
        $tunnelNum = count(self::$tunnels);

        // 获取缓存的通道初始序号
        $cacheKey = 'captcha-tunnel-' . date('-Ymd-') . $mobile;
        $tunnelIndex = intval(Utils::getCache($cacheKey)) % $tunnelNum;

        // 循环发送所有通道，直到成功
        $times = 0;
        do {
            $tunnel = new self::$tunnels[$tunnelIndex++];
            $result = $tunnel->sendCaptcha($mobile, $captcha);
            $times++;
        } while (!$result && $times < $tunnelNum);

        if ($result) {
            // 发送成功缓存通道初始序号
            Utils::setCache($cacheKey, $tunnelIndex % $tunnelNum, 60 * 60 * 24);
        } else {
            // 如果所有通道都发送失败，则告警
            Utils::alert('所有验证码通道发送失败->' . $mobile);
            return false;
        }
        return $captcha;
    }

    private static function getRandomCaptcha($captcha)
    {
        if ($captcha) {
            return $captcha;
        }
        return sprintf('%04s', mt_rand(0, 9999));
    }
}