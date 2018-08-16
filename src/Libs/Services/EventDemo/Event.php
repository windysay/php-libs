<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 9:42
 */

namespace JMD\Libs\Services\EventDemo;

use JMD\Libs\Services\SmsService;

/**
 * Class Event
 * @package common\components\service
 */

class Event
{
    /**
     * 微服务事件
     */
    const CAPTCHA = 1;//验证码
    const LOANED_FAILED = 2;//打款失败
    const SYSTEM_ORDER_CANCEL = 3;//系统升级打款失败
    const TIME_TO_REPAYMENT = 4;//还款到期提醒

    /**
     * 发送验证码
     * @param $code
     */
    public static function Captcha($code)
    {
        $res = SmsService::sendEvent(18816781114, self::CAPTCHA, [['code', 'appName'], [$code, '九秒贷']]);
        return $res ? true : false;
    }

}