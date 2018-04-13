<?php
/**
 * Created by PhpStorm.
 * User: Summer
 * Date: 2018/4/4
 * Time: 16:03
 */

namespace JMD\Libs\Ivr\Tunnels;

use JMD\Libs\Ivr\Interfaces\IvrInterface;
use JMD\App\Utils;
use JMD\Libs\Ivr\Ivr;

class IvrMoor implements IvrInterface
{
    private $APISecret;
    private $account;
    //webcall接口
    private $webCall = 'http://apis.7moor.com/v20160818/webCall/webCall/';
    //服务号码
    private $serviceNo;
    //被呼叫号码
    private $callPhone;
    //回调地址
    private $callBackUrl;
    //实例
    private static $instance;

    const OWN_TOKEN = 'zudT2@@@P%YUnq#K';

    public static $callbackMessage = [
        0 => '线路繁忙/异常（某些情况下，也可能为被叫拒接/振铃未接/占线/关机/空号）',
        3 => '被叫拒接/振铃未接/占线/关机/空号',
        4 => '被叫已接听',
        5 => '一般情况下为余额不足，请先检查账户资费。(少数情况下可能是线路异常)',
        8 => '线路繁忙/异常（某些情况下，也可能为被叫拒接/振铃未接/占线/关机/空号）',

    ];

    public function __construct()
    {
        $this->APISecret = Utils::getParam('7moor_apisecret');
        $this->account = Utils::getParam('7moor_account');
        $this->serviceNo = Utils::getParam('7moor_serviceno');
        $this->callBackUrl = Utils::getParam('7moor_callbackurl');
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /*
     * 接口调用
     */
    public function send($phone, $orderId)
    {
        $sign = self::ownGenerateSign($orderId);
        $parameters = [
            'Action' => 'Webcall',
            'ServiceNo' => $this->serviceNo,
            'Exten' => $phone,
            //异步
            'WebCallType' => 'asynchronous',
            'CallBackUrl' => $this->callBackUrl,
            'ActionID' => "$orderId@$sign",
        ];

        $url = $this->webCall . $this->account . '?sig=' . $this->generateSign();
        $header = $this->generateHeader();
        $parameters = json_encode($parameters);
        array_push($header, 'Content-Length:' . strlen($parameters));
        $res = Utils::curlPost($parameters, $url, $header);

        $logDir = '/data/7moor/';
        if (!file_exists($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        file_put_contents('/data/7moor/7moor.log', date('Y-m-d H:i:s') . 'webcall ' . $res . "\r\n\r\n", FILE_APPEND);
    }

    public function setCallPhones($phone)
    {
        $this->callPhone = $phone;
    }

    private function generateSign()
    {
        $timeStamp = date('YmdHis');
        return strtoupper(md5($this->account . $this->APISecret . $timeStamp));
    }

    private function generateHeader()
    {
        $timestamp = date('YmdHis');
        return [
            'Accept:application/json',
            'Content-Type:application/json;charset=utf-8',
            'Authorization:' . base64_encode("{$this->account}:$timestamp"),
        ];
    }

    private static function ownGenerateSign($orderId)
    {
        //生成规格 strtoupper( md5( orderId + TOKEN ) )
        return strtoupper(md5($orderId . self::OWN_TOKEN));
    }

    public static function ownVerifySign($sign, $orderId)
    {
        return self::ownGenerateSign($orderId) === trim($sign);
    }

}