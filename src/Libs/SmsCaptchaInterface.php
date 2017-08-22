<?php
namespace Jmd\Libs;

/**
 * 发送验证码抽象接口
 *
 * Interface SmsCaptchaInterface
 * @package jmdsms
 */
interface SmsCaptchaInterface
{
    public function sendCapture($mobile,$code);

}
