<?php
namespace JMD\Libs\Sms\Tunnels;

use JMD\App\Utils;
use JMD\Libs\Sms\Interfaces\VoiceSmsBase;
use JMD\Libs\Sms\Sms;
use JMD\Utils\HttpHelper;

class Winic implements VoiceSmsBase
{

    protected $userName;

    protected $passWord;

    protected $mobile;

    protected $result = null;

    protected $srcResult;

    protected $reqUrl;


    public function __construct($mobile)
    {
        $this->userName =  Utils::getParam('winic_username');
        $this->passWord =  Utils::getParam('winc_password');
        $this->mobile = $mobile;
    }

    /**
     * 发送语音验证码
     *
     * @param null|string $code 验证码
     * @param int $replay 播放几次
     * @return bool|null|string
     */
    public function sendVoiceCaptcha($code = null, $replay = 2)
    {
        $code = Sms::getRandomCaptcha($code);

        $this->reqUrl = $url = (sprintf('http://service.winic.org:8813/Service.asmx/SendVoiceForCode?userName=%s&PassWord=%s&Mobile=%s&Code=%s&DisPlayNbr=&vReplay=%s',
            $this->userName, $this->passWord, $this->mobile, $code, $replay));
        $this->srcResult = HttpHelper::get($url);
        $this->result = $this->parseFormat($this->srcResult);
        $bool = $this->isSuccess();
        if (!$bool) {
            $errors = $this->parseSendError();
            Utils::alert('世纪中正科技短信发送失败->' . $this->mobile, json_encode($errors, 256));
            return $bool;
        }

        return $code;
    }

    public function sendVoiceNotice($tmpId, $is_manual = 0)
    {
        throw new \Exception("todo");
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

        $result = current($this->result);

        list($prefix, $reqId) = explode('/', $result);
        $returns['reqId'] = $reqId;
        $returns['code'] = $prefix;

        switch ($prefix) {
            case '-01':
                $returns['msg'] = '余额不足';
                break;
            case '-03':
                $returns['msg'] = '密码错误';
                break;
            case '-04':
                $returns['msg'] = '参数个数不对或者参数类型错误！';
                break;
            case '-05':
                $returns['msg'] = '找不到主显号码';
                break;
            case '-06':
                $returns['msg'] = '主显号码日流量不足';
                break;
            case '-07':
                $returns['msg'] = '主显号码小时流量不足';
                break;
            case '-11':
                $returns['msg'] = '主显号码正在审核';
                break;
            case '-110':
                $returns['msg'] = '主显号码已停用';
                break;
            case '-12':
                $returns['msg'] = '其它错误';
                break;
            default:
                $returns['msg'] = '短信sdk内部未归类状态';
                break;
        }

        return $returns;

    }


    protected function isSuccess()
    {

        if (empty($this->result) || !is_array($this->result)) {
            return false;
        }

        $result = current($this->result);

        list($prefix, $reqId) = explode('/', $result);
        return strcmp($prefix, '000') === 0;
    }


    protected function parseFormat(string $result)
    {
        return json_decode(json_encode(simplexml_load_string($result)), true);
    }


}