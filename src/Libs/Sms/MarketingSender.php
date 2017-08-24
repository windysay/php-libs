<?php
namespace JMD\Libs\Sms;

use JMD\App\Interfaces\MarketingByText;
use JMD\Libs\Sms\Tunnels\XuanWu;

class MarketingSender
{
    public static $marketingSdk = XuanWu::class;


    /**
     * 通过自定义文本发送营销短信
     *
     * @param  string|integer $mobile  手机号码
     * @param string  $text 文本
     * @return bool 成功返回true否则返回false
     */
    public static function sendByText($mobile, $text)
    {
        $obj = new self::$marketingSdk;
        if (!($obj instanceof MarketingByText)) {
            return false;
        }

        return $obj->sendMarketingByText($mobile, $text);
    }
}