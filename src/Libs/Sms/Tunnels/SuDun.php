<?php

namespace JMD\Libs\Sms\Tunnels;

use common\models\BaseModel;
use JMD\App\Utils;
use JMD\Libs\Sms\Interfaces\Captcha;
use JMD\Libs\Sms\Interfaces\Marketing;
use JMD\Libs\Sms\Interfaces\NoticeByTemplate;
use yii\base\Model;

class SuDun implements Captcha, NoticeByTemplate, Marketing
{
    const MIN_SEND_NUM = 10000; //单批次手机号码最少一万个
    const MAX_SEND_NUM = 100000; //单批次手机号码最多十万个

    const URL = 'http://106.15.72.14:8088/sms.aspx';

    private static $config = [
        'userid' => '172',
        'account' => 'jiumiaodai',
        'password' => '6h4YRFLd',
    ];

    /**
     * 发送验证码
     * @param $mobile
     * @param $code
     */
    public function sendCaptcha($mobile, $code)
    {
        // TODO: Implement sendCaptcha() method.
    }

    /**
     * 模板发送通知
     * @param $mobile
     * @param $tempId
     * @param array $tempPara
     */
    public function sendNoticeByTemplate($mobile, $tempId, $tempPara = [])
    {
        // TODO: Implement sendNoticeByTemplate() method.
    }

    /**
     * 发送营销短信【单条】
     * @param $mobile
     * @param $template
     * @param $params
     * @param $sendTime
     */
    public function sendMarketing($mobile, $template, $params)
    {
        // TODO: Implement sendMarketingByText() method.
    }

    /**
     * 批量发送营销短信
     * @param $mobile
     * @param $template
     * @param $params
     * @param string $sendTime
     * @return mixed
     */
    public function batchSendMarketing($mobile = [], $template, $params, $sendTime = '')
    {
        if (empty($mobile)) {
            return Utils::output(1, '号码不能为空', $mobile);
        }

        # 如果是以,分隔的转成字符串
        if (is_string($mobile)) {
            $mobile = explode(',', $mobile);
        }

        # 总号码数量
        $telCount = count($mobile);

        # 最少一万个起发
//        if ($telCount < self::MIN_SEND_NUM) {
//            return "批量发送最少1万个起";
//        }

        # 发送时间为9:00-18:00
        if (date('H') >= 18 || date('H') < 9) {
            return Utils::output(1, '发送时间为9:00-18:00', $mobile);
        }

        $tpl = self::unSensitiveWord(Utils::textReplace(NoticeByTemplate::TEMPLATES[$template], $params));

        # 限制每次最多100000个
        if ($telCount > self::MAX_SEND_NUM) {
            $index = $telCount / self::MAX_SEND_NUM;
            $info = [];
            for ($i = 0; $i <= $index; $i++) {
                $telGroup = array_slice($mobile, $i * self::MAX_SEND_NUM, self::MAX_SEND_NUM);
                // 发送短信
                $info[] = $this->sendSMS($telGroup, $tpl);
            }
        } else {
            $info = $this->sendSMS($mobile, $tpl);
        }

        return $info;
    }

    /**
     * @param $mobile
     * @param $text
     * @param string $sendTime
     * @return bool
     */
    public function sendSMS($mobile, $text, $sendTime = '')
    {

        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }

        $arr = [
            'mobile' => $mobile,
            'content' => $text . "。",
            'sendTime' => $sendTime,
            'action' => 'send',
            'extno' => '',
        ];

        $post = array_merge(self::$config, $arr);

        $output = Utils::curlPost($post, self::URL);

        $status = Utils::object_to_array(Utils::parseResponseAsSimpleXmlElement($output))['message'];

        if ($status != 'ok') {
            Utils::alert('速盾短信发送失败', json_encode(['status' => $status, 'post' => $post]));
            return false;
        } else {
            return true;
        }
    }

    /**
     * 防止屏蔽敏感词，用特殊符号隔开
     * @param $text
     * @return mixed
     */
    public static function unSensitiveWord($text)
    {
        $arr = ['放款', '到账', '领取', '额度', '拿钱', '微信', '下款'];
        $symbol = '.';
        $obj = '';
        foreach ($arr as $value) {
            $text = str_replace($value, mb_substr($value, 0, 1) . $symbol . mb_substr($value, 1, 1), $text);
        }

        return $text;
    }

}