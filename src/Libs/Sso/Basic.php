<?php

namespace JMD\Libs\Sso;

use GuzzleHttp\Client;
use JMD\Libs\Sso\Interfaces\SsoIdentity;

class Basic implements SsoIdentity
{

    private $app_id = 'unvfY8hG';
    private $app_secret = 'be3508150dedfdca158d2697c6645043';

    public function __construct($app_id = '', $app_secret = '')
    {
        if ($app_id) {
            $this->app_id = $app_id;
        }
        if ($app_secret) {
            $this->app_secret = $app_secret;
        }
    }


    public function sign($timestamp)
    {
        $app_id = $this->app_id;
        $app_secret = $this->app_secret;
        return md5(md5($app_id . $app_secret . $timestamp));
    }

    public function sso_request_carry_url()
    {
        $timestamp = time();
        $sign = $this->sign($timestamp);
        $str = [
            'timestamp' => $timestamp,
            'appid' => $this->app_id,
            'sign' => $sign,
        ];
        return http_build_query($str);
    }

    /**
     * @param $url
     * @param $method
     * @param array|null $data
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function httpClient($url, $method, array $data = null)
    {
        $client = new Client();
        $request_data = [
            ['timeout' => 1],
            ['verify' => false]
        ];
        if ($data) {
            $request_data = array_merge($request_data, $data);
        }
        $res = $client->request($method, $url, $request_data);
        return $res->getBody();
    }

    public static function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (strpos($ip, ",") !== false) {
            $ip = explode(",", $ip)[0];
        }
        return $ip;
    }

    /**
     * 获取员工信息列表
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function curl_admin_data()
    {
        $load_domain_config = DomainConfig::base_domain();
        $domain = strstr($load_domain_config['http_sso_check'], 'sso/token', true);
        $basic = new self();
        $url = $domain . 'user/info?' . $basic->sso_request_carry_url();
        return Basic::httpClient($url, 'get');
    }

    /**
     * 添加员工
     * @param $data
     * @return mixed
     */
    public static function curl_admin_add($data)
    {
        $load_domain_config = DomainConfig::base_domain();
        $domain = strstr($load_domain_config['http_sso_check'], 'sso/token', true);
        $basic = new self();
        $staff_data = http_build_query($data);
        $url = $domain . 'api/staff/add?' . $basic->sso_request_carry_url() . '&' . $staff_data;
        return Basic::httpClient($url, 'get');
    }

    /**
     * 修改员工信息
     * @param $data
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function curl_admin_edit($data)
    {
        $load_domain_config = DomainConfig::base_domain();
        $domain = strstr($load_domain_config['http_sso_check'], 'sso/token', true);
        $basic = new self();
        $staff_data = http_build_query($data);
        $url = $domain . 'api/staff/edit?' . $basic->sso_request_carry_url() . '&' . $staff_data;
        return Basic::httpClient($url, 'get');
    }

    /**
     * 根据id获取昵称
     * @param $id
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function get_id_info($id)
    {
        $load_domain_config = DomainConfig::base_domain();
        $domain = strstr($load_domain_config['http_sso_check'], 'sso/token', true);
        $basic = new self();
        $url = $domain . 'user/agent?' . $basic->sso_request_carry_url() . '&id=' . $id;
        return Basic::httpClient($url, 'get');
    }

    /**
     * 获取员工列表
     * @param $companyId
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function get_company_user($companyId)
    {
        $load_domain_config = DomainConfig::base_domain();
        $domain = strstr($load_domain_config['http_sso_check'], 'sso/token', true);
        $basic = new self();
        $url = $domain . 'api/get/company?' . $basic->sso_request_carry_url() . '&company_id=' . $companyId;
        return Basic::httpClient($url, 'get');
    }
}