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

        if ($this->accessToken) {
            $url .= '?access_token=' . $this->accessToken;
        }
        if ($this->method == 1) {
            return HttpHelper::post($url, $this->data);
        } else {
            return HttpHelper::get($url);
        }
    }

}