<?php

namespace JMD\Libs\Sso;

use JMD\App\Utils;
use JMD\App\Configs;

class DomainConfig
{
    public static function base_domain()
    {

        if (Configs::isProEnv()) {//正式、预发布
            return [
                'login_redirect_url' => Utils::getParam('login_redirect_url_prod'),
                'http_sso_check' => Utils::getParam('http_sso_check_prod'),
                'cookie_clean_domain' => Utils::getParam('cookie_clean_domain_prod'),
                'ticket_cookie_name' => Utils::getParam('ticket_cookie_name_prod'),
            ];
        } else {//测试
            return [
                'login_redirect_url' => Utils::getParam('login_redirect_url_dev'),
                'http_sso_check' => Utils::getParam('http_sso_check_dev'),
                'cookie_clean_domain' => Utils::getParam('cookie_clean_domain_dev'),
                'ticket_cookie_name' => Utils::getParam('ticket_cookie_name_dev'),
            ];
        }
    }

}
