<?php

namespace JMD\Libs\Sms\Interfaces;

interface SmsBase
{
    public function __construct($mobile, $sendKey, $tplKey, $tplParams, $appName = '', $callBackFun = '');

    public function sendCaptcha();

    public function sendNotice();

    public function sendMarketing();

    public static function sendCustom($mobile = [], $content, $appName = '');

}