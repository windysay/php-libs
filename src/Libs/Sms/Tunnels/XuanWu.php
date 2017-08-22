<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Libs\Sms\Interfaces\Captcha;
use SoapClient;

/**
 * 玄武短信发送接口
 * Class XuanwuSms
 */
class XuanWu implements Captcha
{
    const SMS_YZM = 'yzm'; //验证码通道
    const SMS_YX = 'yx';  // 营销通道

    private $config = [
        'yzm' => [
            'wsdl' => 'http://211.147.239.62/Service/WebService.asmx?wsdl',
            'account' => 'SZJQB@SZJQB',
            'password' => '16FLv5To',
            'log' => '/data/log/sms/xuanwu/',
        ],
        /**
         * 玄武营销短信
         */
        'yx' => [
            'wsdl' => 'http://211.147.239.62/Service/WebService.asmx?wsdl',
            'account' => 'SZJQB1@SZJQB',
            'password' => 'bLfAn6TC',
            'log' => '/data/log/sms/xuanwu/',
        ]
    ];

    private $appName = '';


    public $verifyCodeText = "验证码：{captcha}。请勿告知他人，谨防上当受骗。温馨提示：{appname}未授权任何个人或机构代客户申请，或收取前期费用!!!";


    public function __construct()
    {
        $this->appName = strval(Utils::getParam('app_name'));
    }

    public function sendCaptcha($mobile, $code)
    {
        $text = str_replace([
            '{captcha}',
            '{appname}'
        ], [
            $code,
            $this->appName
        ], $this->verifyCodeText);
        return $this->sendSMS($mobile,$text);
    }


    /**
     * @param $mobile
     * @param string $text
     * @return mixed
     */
    private function sendSMS($mobile, $text, $channel = self::SMS_YZM)
    {
        $config = $this->config[$channel] ?: $this->config[self::SMS_YZM];

        $wsdl = $config['wsdl'];
        $client = new SoapClient($wsdl, ['connection_timeout' => 300, 'keep_alive' => false]);
        $uuid = self::guid();
        $messageData = array(
            'Phone' => $mobile,
            'Content' => $text,
            'vipFlag' => 'false',
            'customMsgID' => '',
            'customNum' => ''
        );
        $mtpacktmp = array(
            'uuid' => $uuid,
            'batchID' => $uuid,
            'batchName' => date('Y-m-d'),
            'sendType' => '1',
            'msgType' => '1',
            'msgs' => array('MessageData' => $messageData),
            'bizType' => '',
            'distinctFlag' => '',
            'scheduleTime' => '',
            'deadline' => ''
        );
        $result = $client->Post(array(
            'account' => $config['account'],
            'password' => $config['password'],
            'mtpack' => $mtpacktmp
        ));

        $flag = true;
        if ($result->PostResult->result != 0 || $result->PostResult->message != '成功') {
            $flag = false;
            $con = ['tel' => $mobile, 'text' => $text, 'callback' => $result];
            Utils::alert('玄武短信发送失败，请检查', json_encode($con, 256));
        }
        return $flag;
    }

    /**
     * 生成uuid的方法，客户如有其他方法生成，可使用其他方法。
     */
    public static function guid()
    {
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $uuid;
    }
}