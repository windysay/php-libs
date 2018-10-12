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
}