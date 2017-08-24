<?php
namespace JMD\Libs\Sms;

use JMD\Libs\Sms\Interfaces\NoticeByTempId;
use JMD\Libs\Sms\Tunnels\JPush;

/**
 * Class NoticeSender
 * 通知短信发送
 * @package JMD\Libs\Sms
 */
class NoticeSender
{
    public static $noticeSdk = JPush::class;

    /**
     * 通过模版id发送通知短信
     *
     *   138124:验证码：{{code}}。请勿告知他人，谨防上当受骗。温馨提示：九秒贷未授权任何个人或机构代客户申请，或收取前期费用！
     *   139097:由于打款失败，您的订单{{order_no}} 已取消，请及时更换银行卡，不便之处敬请原谅！
     *   139107:提现到内部账户验证码：{{captcha}}(一个小时内有效)
     *   139150:由于系统升级导致当前订单失效，如需继续借款，请前往{{app_name}}APP重新提交订单，给您带来的不便敬请谅解！
     *   139144:尊敬的{{fullname}}，您于{{loan_time}}在{{app_name}}借款{{principal}}元，明天（{{appointment_day}}）是到期还款日。我们将从您尾号为{{bank_no}}的卡上扣除人民币{{repayment_amount}}元，请您务必保证银行卡中余额充足，以免还款逾期产生罚息。
     *   139185:{{money}}元扣款失败，直接在{{app_name}}APP按照提示就可以自助还款！您也可以通过支付宝主动还款，备注姓名和手机号。<支付宝：jiumiaodai@163.com(刘凤)>。如有疑问，直接拨打{{telephone}}咨询。
     *   139127:{{fullname}}您好，{{app_nickname}}善意提醒，您{{principal}}元借款已经逾期{{overdays}}天，今天应还款金额为{{repayment_amount}}元，直接在{{app_name}}APP按照提示就可以自助还款！您也可以通过支付宝主动还款，备注姓名和手机号。<支付宝：jiumiaodai@163.com（刘凤）>。如有疑问，直接拨打{{telephone}}咨询。
     *   139121:尊敬的{{fullname}}您的费用{{money}}元，已经结算。如有疑问，直接拨打{{telephone}}咨询。
     *   139120:尊敬的{{fullname}}您好，我们已收到您的还款{{money}}元，如有疑问，请给{{wx_name}}微信号留言，或直接拨打{{telephone}}咨询。感谢您对{{app_name}}的信任。
     *   139112:尊敬的{{fullname}}，您{{date}}在{{app_name}}借款{{money}}元已成功汇到您的（{{bank_card}}）银行卡。
     *
     * @param string|integer $mobile 手机号码
     * @param string|integer $tempId 模版id,例：138124
     * @param array $tempPara 模版参数，例：['code'=>1111]
     * @return bool 发送成功返回true否则false
     */
    public static function sendByTempId($mobile, $tempId, $tempPara = [])
    {
        $obj = new self::$noticeSdk;
        if (!($obj instanceof NoticeByTempId)) {
            return false;
        }
        return $obj->sendNoticeByTempId($mobile, $tempId, $tempPara);
    }

}

