<?php
namespace JMD\Libs\Risk;

use JMD\Libs\Risk\Interfaces\Request;
use JMD\Utils\HttpHelper;


class BaseRequest implements Request
{
    public $data;

    public $url;

    public $domain = 'http://jmd-service.com/';

    public $method = 1; //1 post 2 get

    public $accessToken;

    protected $appKey;

    protected $secretKey;


    public function __construct($appKey,$secretKey)
    {
        $this->appKey = $appKey;
        $this->secretKey = $secretKey;
    }


    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }


    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    public function setMethod($type)
    {
        $this->method = $type;
        return $this;
    }


    //TODO 有需要验证数据的，通过继承该类，采用模版方法模式
    public function verify()
    {
        return true;
    }

    public function execute()
    {
        if (!$this->verify()) {
            return false;
        }


        $url = $this->domain . $this->url;

        $this->data['app_key'] = $this->appKey;
        $this->data['secret_key'] = $this->secretKey;
        $this->data['timestamp'] = date('Y-m-d H:i:s');
        ksort($this->data);

//        $keys = array_map('strval',$this->data);

        if ($this->method == 1) {
            if (is_array($this->data)) {
                foreach ($this->data as &$val) {
                    if (is_array($val)) {
                        $val = json_encode($val);
                    }
                    $val = strval($val);
                }
            }
            $this->data['sign'] =  md5(json_encode($this->data));
            $result= HttpHelper::post($url, $this->data);
            echo $result;
            return $result;
        } else {
            return HttpHelper::get($url);
        }
    }

}