<?php
namespace Jmd\Libs;

use jmdsms\SmsVerifyCodeInterface;
use Yii;

/**
 * 极光短信
 * Class JSms
 * @package common\components\jpush
 */
class JSms implements SmsVerifyCodeInterface
{
    const URL = 'https://api.sms.jpush.cn/v1/';
    const TEMPLATE_CAPTCHA = 138124;  //验证码：{{code}}。请勿告知他人，谨防上当受骗。温馨提示：九秒贷未授权任何个人或机构代客户申请，或收取前期费用！
    const TEMPLATE_LOANED_FAILED = 139097;  //由于打款失败，您的订单{{order_no}} 已取消，请及时更换银行卡，不便之处敬请原谅！
    const LOANED_MONEY_TO_JMD = 139107;  //提现到内部账户验证码：{{captcha}}(一个小时内有效)
    const SYSTEM_ORDER_CANCEL = 139150;  //由于系统升级导致当前订单失效，如需继续借款，请前往{{app_name}}APP重新提交订单，给您带来的不便敬请谅解！
    const TIME_TO_REPAYMENT = 139144;  //尊敬的{{fullname}}，您于{{loan_time}}在{{app_name}}借款{{principal}}元，明天（{{appointment_day}}）是到期还款日。我们将从您尾号为{{bank_no}}的卡上扣除人民币{{repayment_amount}}元，请您务必保证银行卡中余额充足，以免还款逾期产生罚息。
    const REPAYMENT_FAILED = 139185;  //{{money}}元扣款失败，直接在{{app_name}}APP按照提示就可以自助还款！您也可以通过支付宝主动还款，备注姓名和手机号。<支付宝：jiumiaodai@163.com(刘凤)>。如有疑问，直接拨打{{telephone}}咨询。
    const OVERDUE_NOTICE = 139127;  //{{fullname}}您好，{{app_nickname}}善意提醒，您{{principal}}元借款已经逾期{{overdays}}天，今天应还款金额为{{repayment_amount}}元，直接在{{app_name}}APP按照提示就可以自助还款！您也可以通过支付宝主动还款，备注姓名和手机号。<支付宝：jiumiaodai@163.com（刘凤）>。如有疑问，直接拨打{{telephone}}咨询。
    const EXTEND_SUCCESS = 139121;  //尊敬的{{fullname}}您的费用{{money}}元，已经结算。如有疑问，直接拨打{{telephone}}咨询。
    const REPAYMENT_FINISH = 139120;  //尊敬的{{fullname}}您好，我们已收到您的还款{{money}}元，如有疑问，请给{{wx_name}}微信号留言，或直接拨打{{telephone}}咨询。感谢您对{{app_name}}的信任。
    const LOAN_SUCCESS = 139112;  //尊敬的{{fullname}}，您{{date}}在{{app_name}}借款{{money}}元已成功汇到您的（{{bank_card}}）银行卡。

    private $appKey;
    private $masterSecret;
    private $options;

    public function __construct(array $options = array())
    {
        $this->appKey = Yii::$app->params['jiguang_app_key'];
        $this->masterSecret = Yii::$app->params['jiguang_sectet_key'];
        $this->options = array_merge([
            'ssl_verify' => true,
            'disable_ssl' => false
        ], $options);
    }

    /**
     * 发送验证码
     * 失败返回Array {"headers":{"0":"HTTP\/1.1 403 ","Server":"nginx","Date":"Wed, 09 Aug 2017 03:27:08 GMT","Content-Type":"application\/json;charset=UTF-8","Content-Length":"51","Connection":"keep-alive","X-Application-Context":"sms-messages-web-api:product:9000"},"body":{"error":{"code":50006,"message":"invalid mobile"}},"http_code":403}
     * 成功返回Array {"headers":{"0":"HTTP\/1.1 200 ","Server":"nginx","Date":"Wed, 09 Aug 2017 03:28:20 GMT","Content-Type":"application\/json","Content-Length":"23","Connection":"keep-alive","X-Application-Context":"sms-messages-web-api:product:9000"},"body":{"msg_id":303799781775},"http_code":200}
     * @param $mobile
     * @param $captcha
     * @param int $temp_id
     * @return mixed
     */
    public function sendCode($mobile, $captcha)
    {
        $path = 'messages';
        $body = array(
            'mobile' => $mobile,
            'temp_id' => self::TEMPLATE_CAPTCHA,
            'temp_para' => ['code' => $captcha],
        );
        $url = self::URL . $path;
        $sms = $this;
        $result = $sms->request('POST', $url, $body);
        return empty($result['body']['msg_id']) ? false : true;
    }

    public static function sendMessage($mobile, $temp_id = self::TEMPLATE_CAPTCHA, array $temp_para = [], $time = null)
    {
        $path = 'messages';
        $body = array(
            'mobile' => $mobile,
            'temp_id' => $temp_id,
            'temp_para' => $temp_para,
        );
        if (isset($time)) {
            $path = 'schedule';
            $body['send_time'] = $time;
        }
        $url = self::URL . $path;
        $sms = new JSms();
        $result = $sms->request('POST', $url, $body);
        return empty($result['body']['msg_id']) ? false : true;
    }


    private function request($method, $url, $body = [])
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Connection: Keep-Alive'
            ),
            CURLOPT_USERAGENT => 'JSMS-API-PHP-CLIENT',
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 120,

            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->appKey . ":" . $this->masterSecret,

            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
        );
        if (!$this->options['ssl_verify']
            || (bool)$this->options['disable_ssl']) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        if (!empty($body)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($body);
        }
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);

        if ($output === false) {
            return "Error Code:" . curl_errno($ch) . ", Error Message:" . curl_error($ch);
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header_text = substr($output, 0, $header_size);
            $body = substr($output, $header_size);
            $headers = array();

            foreach (explode("\r\n", $header_text) as $i => $line) {
                if (!empty($line)) {
                    if ($i === 0) {
                        $headers[0] = $line;
                    } else if (strpos($line, ": ")) {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                }
            }

            $response['headers'] = $headers;
            $response['body'] = json_decode($body, true);
            $response['http_code'] = $httpCode;
        }
        curl_close($ch);
        @file_put_contents(Yii::$app->params["jiguang_sms_log_path"] . 'log-' . date('Y-m-d') . '.log', "\n" . date('Y-m-d H:i:s') . ' - ' . json_encode($response, JSON_UNESCAPED_UNICODE), FILE_APPEND);
        return $response;
    }
}
