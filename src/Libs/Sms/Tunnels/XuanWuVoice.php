<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Common\sendKey;
use JMD\Libs\Sms\Sms;

class XuanWuVoice
{
    protected $accountSid;

    protected $authToken;

    protected $mobile;

    protected $result = null;

    protected $srcResult;

    protected $baseUrl = 'https://api.139130.com:9999/api/v1.0.0';

    protected $captchaUrl = '/voice/verify';

    protected $notificationUrl = '/voice/notify';

    protected $clickCallUrl = '/voice/clickcall';

    protected $appId = '60200f26e90bca7926468af9667f84c4';

    protected $appId_test = '2f5d29866b55adb0872b18ad441550ed';

    public static $configName = 'XuanWuVoice';

    protected $callBackFun;

    public function __construct($mobile, $callBackFun = '')
    {

        $this->accountSid = '01841472566a7b7e918c8a50e652a918';
        $this->authToken = '38a0395343d15ac737ceba00c32b003c';
        $this->mobile = $mobile;
        $this->callBackFun = $callBackFun;
    }

    /**
     * 发送语音验证码
     *
     * @param null|string $code 验证码
     * @param int $replay 播放几次
     * @return bool|null|string
     */
    public function sendVoiceCaptcha($code = null, $playTimes = 2, $calledDisplay = '')
    {
        $code = Sms::getRandomCaptcha($code);
        $timestamp = $this->getTimeStamp();
        $sig = $this->getSig($timestamp);
        $url = $this->getUrl($this->captchaUrl, $sig);
        $authorization = $this->getAuthorization($timestamp);
        $data = $this->getData(10002, $playTimes, $calledDisplay);

        $data['subject']['params'] = [(string)$code];
        $this->srcResult = $this->curlPost($url, $authorization, json_encode($data));
        $this->result = $this->parseFormat($this->srcResult);
        $bool = $this->isSuccess();

        if (!$bool) {
            $errors = $this->parseSendError();
            Utils::alert('玄武语音短信发送失败->' . $this->mobile, json_encode($errors, 256));
            return $bool;
        }
        return $code;
    }

    /*语音通知*/
    public function sendVoiceNotice($templateID, $is_manual = 0)
    {
        $timestamp = $this->getTimeStamp();
        $sig = $this->getSig($timestamp);
        $url = $this->getUrl($this->notificationUrl, $sig);
        $authorization = $this->getAuthorization($timestamp);
        $playTimes = 3;
        if($templateID=='20462'){
            $playTimes = 2;
        }
        $data = $this->getData($templateID, $playTimes, '');
        $this->srcResult = $this->curlPost($url, $authorization, json_encode($data));
        $this->result = $this->parseFormat($this->srcResult);
        $bool = $this->isSuccess();
        try {
            $fun = $this->callBackFun;
            $fun && $fun($this->result['info']['callID'], sendKey::CHANNEL_XUAN_WU);
        } catch (\Exception $exception) {
            Utils::alert('推送记录log失败', json_encode($exception->getMessage(), 256));
        }
        if (!$bool) {
            $errors = $this->parseSendError();
            Utils::alert('玄武语音通知发送失败->' . $this->mobile, json_encode($errors, 256));
            return $bool;
        }
        return $bool;
    }

    /*获取公用数据*/
    protected function getData($templateID = '', $playTimes = 2, $calledDisplay = '')
    {
        $info = $subject = [];
        $info['appID'] = $this->appId;
        $subject['called'] = (string)$this->mobile;
        $subject['calledDisplay'] = (string)$calledDisplay;
        $subject['templateID'] = (string)$templateID;
        $subject['playTimes'] = (int)$playTimes;
        $subject['params'] = [];
        //$timestamp = DateTime::timeMs();
        $timestamp = time() * 1000;
        return ['info' => $info, 'subject' => $subject, 'timestamp' => (string)$timestamp];
    }

    protected function curlPost($url = '', $authorization, $data = '', $timeOut = 10)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // 设置为POST方式
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;charset=utf-8',
            'Accept:application/json;charset=utf-8',
            'Authorization:' . $authorization,
            'Content-Length: ' . strlen($data)]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST数据

        $html = curl_exec($ch);
        if ($errorno = curl_errno($ch)) {
            throw new \Exception(curl_error($ch) . json_encode(curl_getinfo($ch), JSON_UNESCAPED_UNICODE), $errorno);
        }
        curl_close($ch);
        return $html;
    }

    /*错误编码格式化*/
    protected function parseSendError()
    {
        $returns = [
            'msg' => '',
            'error' => 1,
            'code' => 0,
            'reqUrl' => '',//兼容前端可能用到的字段
            'result' => $this->srcResult
        ];
        if (empty($this->result) || !is_array($this->result)) {
            return array_merge($returns, [
                'error' => 0,
                'msg' => '成功'
            ]);
        }
        $result = $this->result['result'];
        $result['code'] == 0 ? $returns['error'] = 0 : '';
        return array_merge($returns, ['code' => $result['code'], 'msg' => $result['description']]);
    }

    /*是否成功*/
    protected function isSuccess()
    {

        if (empty($this->result) || !is_array($this->result)) {
            return false;
        }

        $result = $this->result['result'];
        return $result['code'] === 0;
    }


    protected function parseFormat(string $result)
    {
        return json_decode($result, true);
    }

    /*获取当前时间*/
    protected function getTimeStamp()
    {
        return date('YmdHis');
    }

    /*获取sig字符串*/
    protected function getSig($timestamp = '')
    {
        return strtolower(sha1($this->accountSid . $this->authToken . $timestamp));
    }

    /*获取请求完整url*/
    protected function getUrl($functionUrl = '', $sig = '')
    {
        return $this->baseUrl . $functionUrl . '?sig=' . $sig;
    }

    /*获取Authorization*/
    protected function getAuthorization($timestamp = '')
    {
        return base64_encode($this->accountSid . ':' . $timestamp);
    }

}