<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Common\sendKey;
use JMD\Libs\Sms\Interfaces\Captcha;
use JMD\Libs\Sms\Interfaces\Marketing;
use JMD\Libs\Sms\Interfaces\NoticeByTemplate;
use JMD\Libs\Sms\Interfaces\SmsBase;

/**
 * 天瑞云短信平台
 * 注意：使用前请确保模板ID属于哪个账号，否则会出现找不到模板的情况。
 * 文档地址：http://tr.1cloudsp.com/apiv3#1
 * Class TianRuiYun
 * @package JMD\Libs\Sms\Tunnels
 */
class TianRuiYun implements SmsBase
{
    const URL = 'http://api.1cloudsp.com/api/v2/send';
    const NOTICE = 'notice';
    const MARKETING = 'marketing';

    // 营销通道【该通道使用自动义文本发送，因为必须过人审。】
    private static $configMarketing = [
        'accesskey' => 'if8KjFcpRuH1AeD8',
        'secret' => '5ZwAJjRBhOtdYPZ8872aSrqkLl8XaNB5',
    ];

    // 验证码通道【该通道使用模板ID发送，这样做可以免审。】
    private static $configCaptcha = [
        'accesskey' => 'h6rrhzn2zUitj5Yj',
        'secret' => 'NaybF248dmRWAXJPhFEDMBDhmLjMEpoA',
    ];

    public static $configName = 'tianrui';

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
        $templateId = Utils::getTemplateIdByKey(self::$configName, $this->sendKey);
        if (!$templateId) {
            return false;
        }
        return self::sendNow($this->mobile, $templateId, $this->tplParams, self::$configCaptcha);
    }

    public function sendNotice()
    {
        $templateId = Utils::getTemplateIdByKey(self::$configName, $this->sendKey);
        if (!$templateId) {
            return false;
        }
        return self::sendNow($this->mobile, $templateId, $this->tplParams, self::$configCaptcha);
    }

    /**
     * 营销类短信短信内容必须加 【回T退订】
     * @return bool
     */
    public function sendMarketing()
    {

        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }

        if (is_array($this->mobile)) {
            $this->mobile = implode(',', $this->mobile);
        }

        return self::sendContent($this->mobile, $content . '回T退订', self::$configMarketing);
    }

    /**
     * 自定义文案发送【针对深度定制的文案】
     * @param array $mobile
     * @param $content
     * @param string $appName
     */
    public static function sendCustom($mobile = [], $content, $appName = '', $callBackFun = '')
    {
        self::$appName = $appName ?: Utils::getParam('app_name');
        self::$callBackFun = $callBackFun;
        return self::sendContent($mobile, $content . '回T退订', self::$configMarketing);
    }

    /**
     * 模板ID发送模式
     * @param $mobile
     * @param $templateId
     * @param array $content
     * @return bool
     */
    private static function sendNow($mobile, $templateId, $content = [], $configAccount)
    {

        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }

        $config = [
            'mobile' => $mobile,
            'templateId' => $templateId,
            'content' => implode('##', $content),
            'sign' => '【' . self::$appName . '】',
        ];

        $config = array_merge($config, $configAccount);

        $res = Utils::curlPost($config, self::URL);
        $result = json_decode($res, true);
        if ($result['code'] == 0) {
            $fun = self::$callBackFun;
            $fun && $fun($result['batchId'], sendKey::CHANNEL_TIAN_RUI_YUN);
            return true;
        }

        Utils::alert('天瑞云短信渠道发送失败', json_encode(['post' => $config, 'error' => $result], JSON_UNESCAPED_UNICODE));
        return false;
    }

    /**
     * 自定义内容发送
     * @param $mobile
     * @param $templateId
     * @param array $content
     * @return bool
     */
    private static function sendContent($mobile, $content = [], $configAccount)
    {

        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }

        $config = [
            'mobile' => $mobile,
            'content' => $content,
            'sign' => '【' . self::$appName . '】',
        ];

        $config = array_merge($config, $configAccount);

        $res = Utils::curlPost($config, self::URL);
        $result = json_decode($res, true);

        if ($result['code'] == 0) {
            $fun = self::$callBackFun;
            $fun && $fun($result['batchId'], sendKey::CHANNEL_TIAN_RUI_YUN);
            return true;
        }

        Utils::alert('天瑞云短信渠道发送失败', json_encode(['post' => $config, 'error' => $result], JSON_UNESCAPED_UNICODE));
        return false;
    }
}