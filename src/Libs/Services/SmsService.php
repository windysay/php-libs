<?php

namespace JMD\Libs\Services;

/**
 * 短信入口
 * Class Sms
 * @package JMD\Libs\Sms
 */
class SmsService
{
    public static $url = 'http://sms-chengxs.dev.jiumiaodai.com/api/send';

    ########################  推送接口方法 Start #######################

    /**
     * 短信验证码发送
     * @param $mobile
     * @param $code
     * @param $appName
     * @return mixed
     */
    public static function sendCaptcha($mobile, $code, $app = '')
    {
        $request = new BaseRequest();
        $url = '/send/sms-captcha';
        $request->setUrl($url);
        if(is_array($mobile)){
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'code' => $code,
            'app' => $app,
        ];
        $request->setData($post_data);
        $request->domain = self::$url;
        return json_decode($request->execute(), 256);
    }

    /**
     * 短信模板发送
     * @param $mobile
     * @param $sendKey
     * @param array $tplKey
     * @param array $tplParams
     * @param string $appName
     * @return mixed
     */
    public static function sendTpl($mobile, $sendKey, $tplKey = [], $tplParams = [], $appName = '')
    {
        $request = new BaseRequest();
        $url = '/send/sms-tpl';
        $request->setUrl($url);
        if(is_array($mobile)){
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'sendKey' => $sendKey,
            'tplKey' => $tplKey,
            'tplParams' => $tplParams,
            'appName' => $appName,
        ];
        $request->setData($post_data);
        $request->domain = self::$url;
        return json_decode($request->execute(), 256);
    }

    /**
     * 自定义短信发送
     * @param $mobile
     * @param $content
     * @param string $appName
     * @return mixed
     */
    public static function sendCustom($mobile, $content, $appName = '')
    {
        $request = new BaseRequest();
        $url = '/send/sms-custom';
        $request->setUrl($url);
        if(is_array($mobile)){
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'content' => $content,
            'appName' => $appName,
        ];
        $request->setData($post_data);
        $request->domain = self::$url;
        return json_decode($request->execute(), 256);
    }

    /**
     * 语音短信发送
     * @param $mobile
     * @param $key
     * @param string $appName
     * @return mixed
     */
    public static function sendVoiceByTpl($mobile, $key, $appName = '')
    {
        $request = new BaseRequest();
        $url = '/send/voice-tpl';
        $request->setUrl($url);
        if(is_array($mobile)){
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'key' => $key,
            'appName' => $appName,
        ];
        $request->setData($post_data);
        $request->domain = self::$url;
        return json_decode($request->execute(), 256);
    }

    /**
     * 语音验证码发送
     * @param $mobile
     * @param $code
     * @param $appName
     * @return mixed
     */
    public static function sendVoiceCaptcha($mobile, $code)
    {
        $request = new BaseRequest();
        $url = '/send/voice-captcha';
        $request->setUrl($url);
        if(is_array($mobile)){
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'code' => $code,
        ];
        $request->setData($post_data);
        $request->domain = self::$url;
        return json_decode($request->execute(), 256);
    }

    ########################  推送接口方法 End #######################

}