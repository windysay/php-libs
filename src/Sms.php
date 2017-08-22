<?php
namespace Jmd\Libs;

class Sms
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
    public static function sendCapture($mobile, $captcha)
    {
        $sms = new self();
        return $sms->_sendCode($mobile, $captcha);
    }


    private function _sendCode($mobile, $captcha)
    {
        $sms = $this->getVerifyCodeSms();
        while (!$sms->sendCode($mobile, $captcha)) {
            $sms = $this->getVerifyCodeSms();
            if (!$sms) {
                /** 如果两个渠道都发送失败 则记录日志统计 */
                \Yii::info('所有渠道验证码发送失败->' . $mobile);
                return false;
            }
        }
        return true;
    }


    /**
     * @return SmsVerifyCodeInterface|boolean
     */
    private function getVerifyCodeSms()
    {
        $class = array_get($this->verifyCodeSms,$this->verifyCodeKey++);
        return class_exists($class) ? new $class : false;
    }


}