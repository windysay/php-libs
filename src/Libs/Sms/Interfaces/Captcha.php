<?php
namespace JMD\Libs\Sms\Interfaces;

/**
 * 发送验证码抽象接口
 *
 * @package JMD\Libs\Sms\Interfaces
 */
interface Captcha
{
    public function sendCaptcha($mobile,$code);

}
