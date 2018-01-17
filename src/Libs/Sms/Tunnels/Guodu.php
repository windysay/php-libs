<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Libs\Sms\Interfaces\SmsBase;

class Guodu implements SmsBase
{
    /**
     * base url for sending short message
     * @var string
     */
    const SEND_URL = 'http://124.251.7.68:8000/QxtSms/QxtFirewall';
    const MAX_SEND_NUM = 200; //单批次手机号码最多200个

    public static $configName = 'guodu';

    private static $configMarketing = [
        'OperID' => 'jqbyx',
        'OperPass' => 'jqbyx01',
        'Content_Code' => 1,
    ];
    private static $configNotice = [
        'OperID' => 'jqbyzm',
        'OperPass' => 'jqbyzm',
        'Content_Code' => 1,
    ];

    const RESPONSE_PHRASES = [
        '00' => '短信提交成功',  // 批量短信
        '01' => '短信提交成功',  // 个性化短信
        '02' => 'IP限制',
        '03' => '短信提交成功',  // 单条
        '04' => '用户名错误',
        '05' => '密码错误',
        '06' => '自定义短信手机号个数与内容个数不相等',
        '07' => '发送时间错误',
        '08' => '短信包含敏感内容',  // 黑内容
        '09' => '同天内不能向用户重复发送该短信内容',
        '10' => '扩展号错误',
        '11' => '余额不足',
        '-1' => '短信服务器异常',
    ];

    private $mobile;
    private $sendKey;
    private $tplKey;
    private $tplParams;
    private $appName;

    public function __construct($mobile, $sendKey, $tplKey, $tplParams, $appName)
    {
        $this->mobile = $mobile;
        $this->sendKey = $sendKey;
        $this->tplKey = $tplKey;
        $this->tplParams = $tplParams;
        $this->appName = $appName;
    }

    /**
     * 发送验证码
     * @param $mobile
     * @param $code
     */
    public function sendCaptcha()
    {
        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }

        return $this->sendMessage($this->mobile, $content, self::$configNotice);
    }

    /**
     * 通知类短信
     * @return bool|mixed
     */
    public function sendNotice()
    {
        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }

        return $this->sendMessage($this->mobile, $content, self::$configNotice);
    }

    /**
     * 通知营销发送
     * @return bool|mixed
     */
    public function sendMarketing()
    {
        # 发送时间为9:00-21:00
        if (date('H') >= 21 || date('H') < 9) {
            return Utils::output(1, '发送时间为9:00-21:00', $this->mobile);
        }

        $params = Utils::getKeyToKey($this->tplKey, $this->tplParams);

        $content = Utils::getTextTemplate($this->sendKey, $params);
        if (!$content) {
            return false;
        }

        if (is_string($this->mobile)) {
            $this->mobile = explode(',', $this->mobile);
        }

        # 总的号码数量
        $telCount = count($this->mobile);

        # 限制每次最多200个
        $flag = false;
        if ($telCount > self::MAX_SEND_NUM) {
            $index = ceil($telCount / self::MAX_SEND_NUM);
            for ($i = 0; $i < $index; $i++) {
                $telGroup = array_slice($this->mobile, $i * self::MAX_SEND_NUM, self::MAX_SEND_NUM);
                // 发送短信
                $flag = $this->sendMessage(self::$configMarketing, $telGroup, $content);
            }
        } else {
            $flag = $this->sendMessage(self::$configMarketing, $this->mobile, $content);
        }

        return $flag;
    }

    /**
     * 发送短信
     * @param $config
     * @param $mobile
     * @param $text
     * @return bool|mixed
     */
    public function sendMessage($mobile, $text, $config)
    {
        $data = [
            'DesMobile' => $mobile,
            'Content' => utf8_encode("【" . Utils::getParam('app_name') . "】" . $text)
        ];

        $post = array_merge($data, $config);

        $output = Utils::curlPost($post, self::SEND_URL);
        $number = Utils::object_to_array(Utils::parseResponseAsSimpleXmlElement($output))['code'];

        if (!in_array($number, ['00', '01', '03'])) {
            Utils::alert('国都短信发送失败', json_encode(['output' => $output, 'post' => $post, 'error' => self::RESPONSE_PHRASES[$number]]));
            return false;
        } else {
            return true;
        }
    }

}