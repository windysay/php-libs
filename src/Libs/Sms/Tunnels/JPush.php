<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Common\sendKey;
use JMD\Libs\Sms\Interfaces\SmsBase;

/**
 * 极光短信
 * Class JSms
 * @package common\components\jpush
 */
class JPush implements SmsBase
{
    const URL = 'https://api.sms.jpush.cn/v1/';

    public static $configName = 'jiguang';
    private $appKey;
    private $masterSecret;
    private $options;

    private $mobile;
    private $params;
    private $templateId;
    private $appName;
    private $callBackFun;

    /**
     * JPush constructor.
     * @param $mobile
     * @param $sendKey
     * @param $tplKey
     * @param $tplParams
     * @param string $appName
     * @param string $callBackFun
     */
    public function __construct($mobile, $sendKey, $tplKey, $tplParams, $appName = '', $callBackFun = '')
    {
        $this->appKey = Utils::getParam('jiguang_app_key');
        $this->masterSecret = Utils::getParam('jiguang_sectet_key');
        $this->options = [
            'ssl_verify' => true,
            'disable_ssl' => true //关闭ssl验证
        ];

        $this->mobile = $mobile;
        $this->params = Utils::getKeyToKey($tplKey, $tplParams);
        $this->templateId = Utils::getTemplateIdByKey(self::$configName, $sendKey);
        $this->appName = $appName;
        $this->callBackFun = $callBackFun;
    }

    /**
     * 发送验证码
     * @param $mobile
     * @param $sendKey
     * @param $tplKey
     * @param $tplParams
     * @param $captcha
     * @return bool
     */
    public function sendCaptcha()
    {
        return $this->sendNow($this->mobile, $this->templateId, $this->params);
    }


    public function sendNotice()
    {
        return $this->sendNow($this->mobile, $this->templateId, $this->params);
    }


    public function sendMarketing()
    {
        // TODO: Implement sendNoticeMarketing() method.
    }

    public static function sendCustom($mobile = [], $content, $appName = '')
    {
        // TODO: Implement sendCustom() method.
    }

    /**
     * @param $mobile
     * @param $temp_id
     * @param array $temp_para
     * @param null $time
     * @return bool
     */
    public function sendNow($mobile, $tempId, $params)
    {
        if (!$tempId) {
            return false;
        }

        $path = 'messages';
        $body = array(
            'mobile' => $mobile,
            'temp_id' => $tempId,
            'temp_para' => $params,
        );
        $url = self::URL . $path;
        $sms = $this;
        $result = $sms->request('POST', $url, $body);
        if (empty($result['body']['msg_id'])) {
            Utils::alert('极光短信渠道发送失败', json_encode(['tel' => $mobile, 'templateId' => $tempId, 'params' => $params, 'callback' => $result], 256));
            return false;
        }

        $fun = $this->callBackFun;
        $fun && $fun($result['body']['msg_id'], sendKey::CHANNEL_JPUSH);

        return true;
    }


    /**
     * @param $method
     * @param $url
     * @param array $body
     * @return bool
     */
    private function request($method, $url, $body = [])
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Connection: Keep-Alive'
            ),
            CURLOPT_USERAGENT => 'JSMS-API-PHP-CLIENT',
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 120,

            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->appKey . ":" . $this->masterSecret,

            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
        );
        if (!$this->options['ssl_verify']
            || (bool)$this->options['disable_ssl']
        ) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        if (!empty($body)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($body);
        }
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);

        if ($output === false) {
//            Utils::alert("极光短信推送失败 =》 Error Code:" . curl_errno($ch) . ", Error Message:" . curl_error($ch));
            return false;
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header_text = substr($output, 0, $header_size);
            $body = substr($output, $header_size);
            $headers = array();

            foreach (explode("\r\n", $header_text) as $i => $line) {
                if (!empty($line)) {
                    if ($i === 0) {
                        $headers[0] = $line;
                    } else {
                        if (strpos($line, ": ")) {
                            list ($key, $value) = explode(': ', $line);
                            $headers[$key] = $value;
                        }
                    }
                }
            }
            $response['headers'] = $headers;
            $response['body'] = json_decode($body, true);
            $response['http_code'] = $httpCode;
        }
        curl_close($ch);
        return $response;
    }
}
