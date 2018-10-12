<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Common\sendKey;
use JMD\Common\Template;
use JMD\Libs\Sms\Interfaces\Captcha;
use JMD\Libs\Sms\Interfaces\Marketing;
use JMD\Libs\Sms\Interfaces\NoticeByTemplate;
use JMD\Libs\Sms\Interfaces\SmsBase;
use JMD\Libs\Sms\Sms;

/**
 * 玄武短信发送接口
 * Class XuanwuSms
 */
class XuanWu implements SmsBase
{

//    const SMS_YZM = [
//        'username' => 'SZJQB@SZJQB',
//        'password' => '16FLv5To',
//        'msgtype' => 1,
//    ]; //验证码通道

//    const SMS_YX = [
//        'username' => 'SZJQB1@SZJQB',
//        'password' => 'bLfAn6TC',
//        'msgtype' => 4,
//        'subid' => '',
//    ];  // 营销通道1
//
//    const SMS_YX2 = [
//        'username' => 'SZJQB1@SZJQB1',
//        'password' => 'ned0NVTs',
//        'msgtype' => 4,
//        'subid' => '',
//    ];  // 营销通道2

    const MAX_SEND_NUM = 10000; //批量发最大发送数量
    const URL = 'http://211.147.239.62:9050/cgi-bin/sendsms?';

    private $mobile;
    private $content;
    private $config;
    private $appName;
    public static $configName = 'sms_xuanwu_config';
    public static $captchaAndNoticeName = 'sms_xuanwu_captcha_notice';
    public static $marketingConfigName = 'sms_xuanwu_marketing_config';
    private static $callBackFun;

    public function __construct($mobile, $sendKey, $tplKey, $tplParams, $appName = '', $callBackFun = '')
    {
        $this->mobile = $mobile;
        //判断是否渠道是否可以发送，针对多app时，有些app不支持,检查是否有配置，配置说明支持，没有配置说明不支持
        $this->config = Utils::getParam(Sms::SMS_CONFIG);
        $params = Utils::getKeyToKey($tplKey, $tplParams);
        $this->content = Utils::getTextTemplate($sendKey, $params);
        $this->appName = $appName;
        self::$callBackFun = $callBackFun;
    }


    public function sendCaptcha()
    {
        if (!isset($this->config[Sms::TUNNELS_CONFIG][self::$configName][self::$captchaAndNoticeName])) {
            return false;
        }
        $config = $this->config[Sms::TUNNELS_CONFIG][self::$configName][self::$captchaAndNoticeName];
        return self::sendMessage($this->mobile, $this->content, $config);
    }

    public function sendNotice()
    {
        if (!isset($this->config[Sms::TUNNELS_CONFIG][self::$configName][self::$captchaAndNoticeName])) {
            return false;
        }
        $config = $this->config[Sms::TUNNELS_CONFIG][self::$configName][self::$captchaAndNoticeName];
        return self::sendMessage($this->mobile, $this->content, $config);
    }

    public function sendMarketing()
    {
        // TODO: Implement sendNoticeMarketing() method.
    }

    /**
     * @param array $mobile
     * @param        $content
     * @param string $appName
     * @return mixed
     */
    public static function sendCustom($mobile = [], $content, $appName = '', $callBackFun = '')
    {
        $config = Utils::getParam(Sms::SMS_CONFIG)[Sms::TUNNELS_CONFIG][self::$configName][self::$marketingConfigName];
        // 玄武营销短信后面需要回N退订，否则会被拦截发不出去
        $content = $content . '回N退订';
        self::$callBackFun = $callBackFun;
        return self::sendMessage($mobile, $content, $config);
    }


    /**
     * 发送短信
     * @param       $mobile
     * @param       $text
     * @param array $channel
     * @return bool
     */
    private static function sendMessage($mobile, $text, $channel = [])
    {
        if (empty($channel)) {
            return false;
        }

        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }

        //TODO 检测是否替换完变量

        // 检测文案是否消耗两、三条短信
        Utils::smsTextStrlen($text, '玄武');

        $data = array_merge(['to' => $mobile, 'text' => rawurlencode(mb_convert_encoding($text, 'gbk', 'utf-8'))], $channel);

        // 参数拼接
        $params = self::urlJoin($data);

        $res = Utils::curlGet(self::URL . $params);
        if ($res != 0) {
            Utils::alert('【失败】玄武短信通道发送失败->' . $mobile, json_encode(['params' => $params, 'return' => $res], 256));
            return false;
        }
        if ($res == '-12') {
            Utils::alert('【余额不足】玄武短信通道余额不足，请尽快充值！' . $mobile, json_encode(['params' => $params, 'return' => $res], 256));
            return false;
        }

        /** 玄武没有回调 仅记录渠道 */
        $fun = self::$callBackFun;
        $fun && $fun(0, sendKey::CHANNEL_XUAN_WU);

        return true;
    }

    private static function urlJoin($params = [])
    {
        ksort($params);

        $sign = '';
        foreach ($params as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= $key . '=' . $val . '&';
            }
        }

        $sign = rtrim($sign, '&');

        return $sign;
    }
}