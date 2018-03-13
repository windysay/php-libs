<?php

namespace JMD\Libs\Sms\Tunnels;

use JMD\Libs\Sms\Interfaces\VoiceSmsBase;
use JMD\Libs\Sms\Sms;
use JMD\Utils\HttpHelper;
use JMD\App\Utils;

class MontenNets implements VoiceSmsBase
{

    protected $userName;

    protected $passWord;

    protected $mobile;

    protected $result = null;

    protected $srcResult;

    protected $reqUrl = 'http://61.145.229.28:5001/voice/v2/std/template_send';

    public static $configName = 'MontenNets';


    public function __construct($mobile)
    {
        $this->userName = Utils::getParam('montent_nets_userid');
        $this->passWord = Utils::getParam('montent_nets_pwd');
        $this->mobile = $mobile;
    }

    /**
     * 发送语音验证码
     *
     * @param null|string $code 验证码
     * @return bool|null|string
     */
    public function sendVoiceCaptcha($code = null)
    {
        $code = Sms::getRandomCaptcha($code);

        $bool = $this->send(100004, $this->userName, $this->passWord, 1, $code);

        if (!$bool) {
            return false;
        }

        return $code;
    }

    public function sendVoiceNotice($tmpId)
    {
        return $this->send($tmpId, 'YY0286', '159367', 3);
    }


    public function send($tmpId, $userid, $pwd, $msgType, $content = '')
    {
        $params = [
            'userid' => $userid,
            'pwd' => $pwd,
            'mobile' => $this->mobile,
            'timestamp' => date('mdHis'),
            'exno' => '',
            'cusid' => time(),
            'tmplid' => $tmpId,
            'msgtype' => $msgType
        ];
        if ($content) {
            $params['contnet'] = $content;
        }


        $pwd = "{$params['userid']}00000000{$params['pwd']}{$params['timestamp']}";

        $params['pwd'] = md5($pwd);

        $url = $this->reqUrl;
        $this->srcResult = HttpHelper::curl($url, $params);
        $this->result = $this->parseFormat($this->srcResult);
        $bool = $this->isSuccess();
        if (!$bool) {
            $errors = $this->parseSendError();
            if($msgType == 1){
                Utils::alert('梦网科技语音验证码发送失败->' . $this->mobile, json_encode($errors, 256));
            } else {
                Utils::alert('梦网科技语音通知发送失败->' . $this->mobile, json_encode($errors, 256));
            }

        }
        return $bool;
    }


    protected function parseSendError()
    {
        $returns = [
            'msg' => '',
            'error' => 1,
            'code' => 0,
            'reqUrl' => $this->reqUrl,
            'result' => $this->srcResult
        ];
        if (empty($this->result) || !is_array($this->result)) {
            return array_merge($returns, [
                'error' => 0,
                'msg' => '没有错误'
            ]);
        }

        $result = (array)$this->result;

        if (array_key_exists('msgid', $result)) {
            $returns['msgid'] = $result['msgid'];
        }

        if (array_key_exists('custid', $result)) {
            $returns['custid'] = $result['custid'];
        }

        if (array_key_exists('result', $result)) {
            $returns['result'] = $result['result'];

            switch ($result['result']) {
                case '-400001':
                    $returns['msg'] = '语音验证码请求参数数据格式错误';
                    break;
                case '-400002':
                    $returns['msg'] = '用户验证失败';
                    break;
                case '-400999':
                    $returns['msg'] = '服务器内部错误！';
                    break;
                case '-400004':
                    $returns['msg'] = '网络连接超时';
                    break;
                case '-400005':
                    $returns['msg'] = '重发失败';
                    break;
                case '-400006':
                    $returns['msg'] = '一分钟内同一个号码同一个用户同一个验证码 重复订单';
                    break;
                default:
                    $returns['msg'] = '短信sdk内部未归类状态';
                    break;
            }

        }


        return $returns;

    }


    protected function isSuccess()
    {

        if (empty($this->result) || !is_array($this->result)) {
            return false;
        }

        $result = $this->result;

        return $result['result'] === 0;
    }


    protected function parseFormat(string $result)
    {
        return json_decode($result, true);
    }


}