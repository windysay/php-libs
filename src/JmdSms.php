<?php
namespace jmdsms;


use common\components\jpush\JSms;

class JmdSms
{

    /**
     * 短信验证码sdk数组
     *
     * @var array
     */
    private $verifyCodeSms = [
        XuanWuSms::class, //玄武
        JSms::class // 极光
    ];
    private $verifyCodeKey = 0;


    /**
     * 发送短信验证码
     * @param string|integer $mobile 手机号
     * @param string|integer $captcha 验证码
     * @return bool
     */
    public static function sendCode($mobile, $captcha)
    {
        $sms = new self();
        return $sms->_sendCode($mobile, $captcha);
    }


    private function _sendCode($mobile, $captcha)
    {
        $sms = $this->getVerifyCodeSms();
        while (!$sms->sendCode($mobile, $captcha)) {
            $sms = $this->getVerifyCodeSms();
            if (empty($sms)) {
                return false;
            }
        }
        return true;
    }


    /**
     * @return SmsVerifyCodeInterface
     */
    private function getVerifyCodeSms()
    {
        return $this->verifyCodeSms[$this->verifyCodeKey++];
    }


}