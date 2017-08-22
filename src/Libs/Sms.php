<?php
namespace Jmd\Libs;

class Sms
{

    /**
     * 短信验证码sdk数组
     *
     * @var array
     */
    private $captchaSms = [
        XuanWuSms::class, //玄武
        JSms::class // 极光
    ];

    private $captchaKey;

    private $mobile;


    public function __construct($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * 发送短信验证码
     * @param string|integer $mobile 手机号
     * @return bool|integer 成功返回验证码失败返回false
     */
    public static function sendCapture($mobile)
    {
        $sms = new self($mobile);
        return $sms->sendCode();
    }


    private function sendCode()
    {
        $mobile = $this->mobile;
        $code = $this->getRandomCaptcha();
        do {
            $sms = $this->getCaptchaSms();
            if (!$sms) {
                /** 如果两个渠道都发送失败 则记录日志统计 */
                \Yii::warning('所有渠道验证码发送失败->' . $mobile);
                return false;
            }

        } while (!$sms->sendCapture($mobile, $code));

        return $code;
    }


    /**
     * @return SmsCaptchaInterface|boolean
     */
    private function getCaptchaSms()
    {
        $mobile = $this->mobile;
        $cacheName = 'captcha-count' . $mobile . date('Ymd');

        if ($this->captchaKey === null) {
            $this->captchaKey = intval(\Yii::$app->cache->get($cacheName));
            $this->captchaKey = $this->captchaKey < count($this->captchaSms) ? $this->captchaKey : 0;
            \Yii::$app->cache->set($cacheName, $this->captchaKey + 1, 60 * 60 * 24);
        }

        $class = array_get($this->captchaSms, $this->captchaKey++);
        return $this->captchaKey < count($this->captchaSms) ? new $class : false;
    }


    private function getRandomCaptcha()
    {
        return sprintf('%04s', mt_rand(0, 9999));
    }


}