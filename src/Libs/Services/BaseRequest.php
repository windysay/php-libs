<?php

namespace JMD\Libs\Services;

use App\Helper\Api\SignHelper;
use JMD\App\Configs;
use JMD\App\Utils;
use JMD\Libs\Services\Interfaces\Request;
use JMD\Utils\HttpHelper;

class BaseRequest implements Request
{
    const CONFIG_NAME = 'public_services_config'; //公共服务配置

    public $data;

    public $url;

    public $domain = 'http://api.services.dev.jiumiaodai.com/';

    protected $appKey;

    protected $secretKey;


    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        $config = Utils::getParam(self::CONFIG_NAME);
        $this->appKey = $config['app_key'];
        $this->secretKey = $config['app_secret_key'];
        if (Configs::isProEnv()) {
            $this->domain = 'http://api.services.jiumiaodai.com/';
        }
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
     * @param bool $isPost
     * @return mixed
     * @throws \Exception
     */
    public function execute($isPost = true)
    {
        $url = $this->domain . $this->url;

        $this->data['app_key'] = $this->appKey;
        $this->data['sign'] = SignHelper::sign($this->data, $this->secretKey);
        if ($isPost) {
            $result = HttpHelper::post($url, $this->data, 60);
        } else {
            $result = HttpHelper::get($url);
        }
        return $result;
    }

}