<?php

namespace JMD\Libs\Sso;

use JMD\App\Utils;
use JMD\App\Configs;

class DomainConfig
{
    public static function base_domain()
    {
        if (Configs::isProEnv()) {
            // 正式、预发布
            $loginUrl = Utils::getParam('login_redirect_url_prod');
            if (empty(Utils::getParam('login_redirect_url_prod'))) {
                // 默认值
                $proBase = 'https://sso.jiumiaodai.com/sso/';
                $loginUrl = $proBase . 'login';
                $checkUrl = $proBase . 'token';
                $cookieDomain = '.jiumiaodai.com';
                $ticketName = 'ticket_prod';
            } else {
                $checkUrl = Utils::getParam('http_sso_check_prod');
                $cookieDomain = Utils::getParam('cookie_clean_domain_prod');
                $ticketName = Utils::getParam('ticket_cookie_name_prod');
            }
            return [
                'login_redirect_url' => $loginUrl,
                'http_sso_check' => $checkUrl,
                'cookie_clean_domain' => $cookieDomain,
                'ticket_cookie_name' => $ticketName,
            ];
        } else {
            // 测试
            return [
                'login_redirect_url' => Utils::getParam('login_redirect_url_dev'),
                'http_sso_check' => Utils::getParam('http_sso_check_dev'),
                'cookie_clean_domain' => Utils::getParam('cookie_clean_domain_dev'),
                'ticket_cookie_name' => Utils::getParam('ticket_cookie_name_dev'),
            ];
        }
    }

}
