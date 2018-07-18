<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Common\sendKey;
use JMD\Libs\Sms\Interfaces\SmsBase;

/**
 * 星云享短信平台
 * Class XingYunXiang
 * @package JMD\Libs\Sms\Tunnels
 */
class XingYunXiang implements SmsBase
{
    const URL = 'http://60.205.151.174:8888/v2sms.aspx';

    // 验证码通道【该通道使用模板ID发送，这样做可以免审。】
    private static $configCaptcha = [
        'userid' => '296',
    ];
    private static $configCaptchaAdmin = [
        'account' => 'jiumiaodai',
        'password' => '123456'
    ];

    // 营销通道
    private static $configMarketing = [
        'userid' => '329',
    ];
    private static $configMarketingAdmin = [
        'account' => 'jmd-yx',
        'password' => 'jmd@2017'
    ];

    public static $configName = 'xingyunxiang';

    private $mobile;
    private $sendKey;
    private $tplKey;
    private $tplParams;
    private static $appName;
    private static $callBackFun;

    public function __construct($mobile, $sendKey, $tplKey, $tplParams, $appName = '', $callBackFun = '')
    {
        $this->mobile = $mobile;
        $this->sendKey = $sendKey;
        $this->tplKey = $tplKey;
        $this->tplParams = $tplParams;
        self::$appName = $appName ?: Utils::getParam('app_name');
        self::$callBackFun = $callBackFun;
    }

    public function sendCaptcha()
    {
        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }
        return self::sendSms($this->mobile, $content, self::$configCaptcha, self::$configCaptchaAdmin);

    }

    public function sendNotice()
    {
        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }
        return self::sendSms($this->mobile, $content, self::$configCaptcha, self::$configCaptchaAdmin);
    }

    public function sendMarketing()
    {
        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }
        return self::sendSms($this->mobile, $content, self::$configMarketing, self::$configMarketingAdmin);
    }

    public static function sendCustom($mobile = [], $content, $appName = '', $callBackFun = '')
    {
        if(!$appName){
            return false;
        }

        self::$appName = $appName;
        self::$callBackFun = $callBackFun;
        return self::sendSms($mobile, $content, self::$configMarketing, self::$configMarketingAdmin);
    }

    /**
     * @param $mobile
     * @param array $content
     * @param $configAccount
     * @param $configAccountAdmin
     * @return bool
     */
    public static function sendSms($mobile, $content = [], $configAccount, $configAccountAdmin)
    {
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }

        $content = '【' . self::$appName . '】' . $content;
        // 检测文案是否消耗两、三条短信
        Utils::smsTextStrlen($content,'星云享');

        $timestamp = date('YmdHis');
        $arr = [
            'mobile' => $mobile,
            'content' => $content,
            'timestamp' => $timestamp,
            'action' => 'send',
            'sign' => self::sign($configAccountAdmin, $timestamp),
        ];

        $config = array_merge($configAccount, $arr);

        $res = Utils::curlPost($config, self::URL);

        $xmlData = Utils::object_to_array(Utils::parseResponseAsSimpleXmlElement($res));

        if (isset($xmlData['returnstatus']) && $xmlData['returnstatus'] == 'Success') {
            $fun = self::$callBackFun;
            $fun && $fun($xmlData['taskID'], sendKey::CHANNEL_XING_YUN_XIANG);
            return true;
        }

        return false;
    }

    /**
     * 使用 账号+密码+时间戳 生成MD5字符串作为签名。MD5生成32位，且需要小写
     * @param $timestamp
     * @return string
     */
    private static function sign($config, $timestamp)
    {
        $configAdmin = implode('', $config);
        return md5($configAdmin . $timestamp);
    }
}