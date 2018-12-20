<?php

namespace JMD\Common;

use JMD\App\Utils;
use JMD\Utils\HttpHelper;
use JMD\Utils\SignHelper;

class BaseRequest
{
    public $configName; //公共服务配置

    public $data;

    public $url;

    protected $endpoint;

    protected $appKey;

    protected $secretKey;

    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        $config = Utils::getParam($this->configName);
        $this->appKey = $config['app_key'];
        $this->secretKey = $config['app_secret_key'];
        $this->endpoint = $config['endpoint'];
    }

    /**
     * @param $appKey
     * @return $this
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
        return $this;
    }

    /**
     * @param $secretKey
     * @return $this
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @param bool $isPost
     * @return DataFormat
     * @throws \Exception
     */
    public function execute($isPost = true)
    {
        $url = $this->endpoint . $this->url;

        $this->data['app_key'] = $this->appKey;
        $this->data['sign'] = SignHelper::sign($this->data, $this->secretKey);
        if ($isPost) {
            $result = HttpHelper::post($url, $this->data, 60);
        } else {
            $result = HttpHelper::get($url);
        }
        return new DataFormat($result);
    }

}