<?php

namespace JMD\Libs\Services;

use JMD\App\Configs;
use JMD\App\Utils;
use JMD\Libs\Risk\Interfaces\Request;
use JMD\Utils\HttpHelper;

class BaseRequest implements Request
{
    const CONFIG_NAME = 'base_services_config';

    public $data;

    public $url;

    public $domain = 'http://services.api.dev.jiumiaodai.com/';

    protected $appKey;

    protected $secretKey;


    /**
     * BaseRequest constructor.
     * @param $appKey
     * @param $secretKey
     */
    public function __construct($appKey, $secretKey)
    {
        $config = Utils::getParam(self::CONFIG_NAME);
        $this->appKey = $config['app_key'];
        $this->secretKey = $config['app_secret_key'];
        if (Configs::isProEnv()) {
            $this->domain = 'http://services.api.jiumiaodai.com';
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
     * @return mixed
     * @throws \Exception
     */
    public function execute()
    {
        $url = $this->domain . $this->url;

        $this->data['app_key'] = $this->appKey;
        $this->data['secret_key'] = $this->secretKey;
        $this->data['timestamp'] = date('Y-m-d H:i:s');
        ksort($this->data);

        if (is_array($this->data)) {
            foreach ($this->data as &$val) {
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                $val = strval($val);
            }
        }
        $this->data['sign'] = md5(json_encode($this->data));
        $result = HttpHelper::post($url, $this->data, 60);
        return $result;
    }

}