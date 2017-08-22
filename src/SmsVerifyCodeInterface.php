<?php
namespace Jmd\Libs;

/**
 * 发送验证码抽象接口
 *
 * Interface SmsVerifyCodeInterface
 * @package jmdsms
 */
interface SmsVerifyCodeInterface
{
    public function sendCode($mobile,$code);

}
