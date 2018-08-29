<?php

namespace JMD\Libs\Services;

use JMD\App\Configs;

/**
 * 短信入口
 * Class Sms
 * @package JMD\Libs\Sms
 */
class SmsService
{
    public static $url = 'http://sms-chengxs.dev23.jiumiaodai.com/';

    ########################  推送接口方法 Start #######################

    /**
     * 短信验证码发送
     * @param $mobile
     * @param $code
     * @param $app_name
     * @return DataFormat
     * @throws \Exception
     */
    public static function sendCaptcha($mobile, $code, $app_name = '')
    {
        $request = new BaseRequest();
        $url = 'api/send/sms-captcha';
        $request->setUrl($url);
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'code' => $code,
            'app_name' => $app_name,
        ];
        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    /**
     * 短信模板发送
     * @param $mobile
     * @param $sendKey
     * @param array $tplKey
     * @param array $tplParams
     * @param string $app_name
     * @return DataFormat
     * @throws \Exception
     */
    public static function sendTpl($mobile, $sendKey, $tplKey = [], $tplParams = [], $app_name = '')
    {
        $request = new BaseRequest();
        $url = 'api/send/sms-tpl';
        $request->setUrl($url);
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'sendKey' => $sendKey,
            'tplKey' => $tplKey,
            'tplParams' => $tplParams,
            'app_name' => $app_name,
        ];
        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    /**
     * 自定义短信发送
     * @param $mobile
     * @param $content
     * @param string $app_name
     * @return DataFormat
     * @throws \Exception
     */
    public static function sendCustom($mobile, $content, $app_name = '')
    {
        $request = new BaseRequest();
        $url = 'api/send/sms-custom';
        $request->setUrl($url);
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'content' => $content,
            'app_name' => $app_name,
        ];
        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    /**
     * 语音短信发送
     * @param $mobile
     * @param $key
     * @return DataFormat
     * @throws \Exception
     */
    public static function sendVoiceByTpl($mobile, $key)
    {
        $request = new BaseRequest();
        $url = 'api/send/voice-tpl';
        $request->setUrl($url);
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'key' => $key,
        ];
        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    /**
     * 语音验证码发送
     * @param $mobile
     * @param $code
     * @return DataFormat
     * @throws \Exception
     */
    public static function sendVoiceCaptcha($mobile, $code)
    {
        $request = new BaseRequest();
        $url = 'api/send/voice-captcha';
        $request->setUrl($url);
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'code' => $code,
        ];
        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    /**
     * 通过报告id获取报告结果
     * @param $report_ids
     * @return mixed
     */
    public static function getReport($report_ids)
    {
        $request = new BaseRequest();
        $url = 'api/send/get-report';
        $request->setUrl($url);
        if(is_array($report_ids)){
            $report_ids = implode(',', $report_ids);
        }
        $post_data = [
            'report_ids' => $report_ids,
        ];
        $request->setData($post_data);
        if(!Configs::isProEnv()){
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    /**
     * 通过事件推送 调用试例 EventDemo/Event.php
     * @param $mobile
     * @param $event_id
     * @param $vals
     * @param string $appName
     * @return DataFormat
     */
    public static function sendEvent($mobile, $event_id, $vals, $appName = '')
    {
        $request = new BaseRequest();
        $url = 'api/send/push-event';
        $request->setUrl($url);
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $post_data = [
            'mobile' => $mobile,
            'event_id' => $event_id,
            'vals' => $vals,
            'app_name' => $appName,
        ];
        $request->setData($post_data);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }

    public static function getEventList()
    {
        $request = new BaseRequest();
        $url = 'api/send/get-event-list';
        $request->setUrl($url);
        if (!Configs::isProEnv()) {
            $request->domain = self::$url;
        }
        return $request->execute();
    }
    ########################  推送接口方法 End #######################
}