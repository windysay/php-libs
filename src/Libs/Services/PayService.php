<?php

namespace JMD\Libs\Services;

use JMD\App\Utils;
use JMD\Utils\SignHelper;

/**
 * 支付入口
 */
class PayService
{
    /**
     * @var string 单笔出款地址接口
     */
    public $singlePayUrl = 'api/pay/singlePay';

    /**
     * @var string 出款结果查询接口
     */
    public $remitQueryOrderUrl = 'api/pay/remitQueryOrder';

    /**
     * @var string 余额查询地址接口
     */
    public $queryBalanceUrl = 'api/pay/queryBalance';
    /**
     * @var string 支付短验发送
     */
    public $sendCodeUrl = 'api/trade/send_code';
    /**
     * @var string 支付短验重发接口
     */
    public $againSendCodeUrl = 'api/trade/again_send_code';
    /**
     * @var string 确认支付短验接口
     */
    public $confirmPayUrl = 'api/trade/confirm_pay';
    /**
     * @var string 单笔出款地址接口
     */
    public $single_payUrl = 'api/trade/single_pay';
    /**
     * @var string 单笔出款地址接口
     */
    public $singleRepayCollectUrl = 'api/trade/single_repay_collect';
    /**
     * @var string 单笔退款接口
     */
    public $refundUrl = 'api/trade/refund';

    /**
     * @var array 请求参数
     */
    public $params = [];


    /**
     * @var bool 验签结果
     */
    public $checkSign;

    /**
     * @var mixed 通知结果
     */
    public $result;

    /**
     * @var bool 交易结果
     */
    public $tradeSuccess;

    /**
     * @var BaseRequest
     */
    public $request;

    public function __construct()
    {
        $this->request = new BaseRequest();

//        $this->request->domain = 'http://local.services.com/';
//        $this->request->domain = 'http://zy-api.services.dev23.jiumiaodai.com/';

    }

    /**
     * 单笔出款
     */
    public function SinglePay()
    {
        $this->request->setUrl($this->singlePayUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    public function setParams($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * 出款通知
     */
    public function remitNotice()
    {
        $noticeInfo = file_get_contents("php://input");
        $this->result = json_decode($noticeInfo, true);

        //写入日志文件
        $file = '/data/log/pay/' . date('Y_m_d') . '_remitNotice.log';
        $log = sprintf('[%s]---%s%s', date('Y-m-d H:i:s'), $this->result['orderNo'] ?? '',
            PHP_EOL . $noticeInfo . PHP_EOL . PHP_EOL);
        @file_put_contents($file, $log, FILE_APPEND);

        //验签
        $this->checkSign();

        //交易结果
        $this->tradeSuccess = $this->result['status'] === 'SUCCESS';

        return $this->result;
    }

    /**
     * 通知
     */
    public function notice()
    {
        $noticeInfo = file_get_contents("php://input");
        $this->result = json_decode($noticeInfo, true);

        //写入日志文件
        $file = '/data/log/pay/' . date('Y_m_d') . '_remitNotice.log';
        $log = sprintf('[%s]---%s%s', date('Y-m-d H:i:s'), $this->result['orderNo'] ?? '',
            PHP_EOL . $noticeInfo . PHP_EOL . PHP_EOL);
        @file_put_contents($file, $log, FILE_APPEND);

        //验签
        $this->checkSign();

        //交易结果
        $this->tradeSuccess = $this->result['status'] === 'SUCCESS';

        return $this->result;
    }

    /**
     *
     *
     */
    private function checkSign()
    {
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);

        $this->checkSign = SignHelper::validateSign($this->result, $config['app_secret_key']);
    }

    /**
     * 通过订单编号查询订单
     */
    public function remitQueryOrder()
    {
        $this->request->setUrl($this->remitQueryOrderUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    /**
     * 查询余额
     */
    public function queryBalance()
    {
        $this->request->setUrl($this->queryBalanceUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    /**
     * 实时支付发送短验
     */
    public function sendCode()
    {
        $this->request->setUrl($this->sendCodeUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    /**
     * 实时支付短验重发
     */
    public function againSendCode()
    {
        $this->request->setUrl($this->againSendCodeUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    /**
     * 确认支付
     */
    public function confirmPay()
    {
        $this->request->setUrl($this->confirmPayUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }


    /**
     * 单笔代付
     */
    public function single_pay()
    {
//        //todo 测试
//        $endpoint = 'http://zy-api.services.dev23.jiumiaodai.com/';
//        $this->request->setEndpoint($endpoint);

        $this->request->setUrl($this->single_payUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    /**
     * 单笔代扣
     */
    public function singleRepayCollect()
    {
        $this->request->setUrl($this->singleRepayCollectUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

    /**
     * 单笔退款
     */
    public function refund()
    {
        $this->request->setUrl($this->refundUrl);
        $this->request->setData($this->params);

        return $this->request->execute()->getData();
    }

}