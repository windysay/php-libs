<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/7
 * Time: 11:06
 */

namespace JMD\Libs\Sms\Tunnels;


use JMD\App\Utils;
use JMD\Libs\Sms\Interfaces\SmsBase;
use JMD\Utils\HttpHelper;

class Nexmo implements SmsBase
{

    private $api_key = '3c9949b3';
    private $api_secret = 'jlo7kaye5FB3RDFA';
    private $sms_url = 'https://rest.nexmo.com/sms/json';
    private $mobile = '';
    private $content = '';
    private $appName = '';
    private $callBackFun = '';

    public function __construct($mobile, $sendKey, $tplKey, $tplParams, $appName = 'Cash-Now', $callBackFun = '')
    {
        $this->mobile = $mobile;
        $params = Utils::getKeyToKey($tplKey, $tplParams);
        $this->content = Utils::getTextTemplate($sendKey, $params);
        $this->appName = $appName;
        $this->callBackFun = $callBackFun;
    }

    public function sendCaptcha()
    {
        $result = $this->sendSMS($this->mobile, $this->content, $this->appName);
        if (isset($result['messages']['status']) && ($result['messages']['status'] == 0)) {
            $fun = $this->callBackFun;
            $fun && $fun($result['messages']['message-id']);
        } else {
            Utils::alert('nexmo短信渠道发送失败', json_encode(['tel' => $this->mobile, 'content' => $this->content, 'callback' => $result], 256));
            return false;
        }
        return true;
    }

    public function sendNotice()
    {
        $result = $this->sendSMS($this->mobile, $this->content, $this->appName);
        if (isset($result['messages'][0]['status']) && ($result['messages'][0]['status'] == 0)) {
            $fun = $this->callBackFun;
            $fun && $fun($result['messages'][0]['message-id']);
        } else {
            Utils::alert('nexmo短信渠道发送失败', json_encode(['tel' => $this->mobile, 'content' => $this->content, 'callback' => $result], 256));
            return false;
        }
        return true;
    }

    public function sendMarketing()
    {
        // TODO: Implement sendMarketing() method.
    }

    public static function sendCustom($mobile = [], $content, $appName = '')
    {
        // TODO: Implement sendCustom() method.
    }


    private function sendSMS($mobile, $text, $from)
    {
        $data = [
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'to' => $mobile,
            'from' => $from,
            'text' => $text,
        ];
        $data_json = json_encode($data);

        $ch = curl_init($this->sms_url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json'
            ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }


}