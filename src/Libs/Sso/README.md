## sso

#安装使用
 - 1.必须安装 composer require guzzlehttp/guzzle 组件
 - 2.必须安装 redis扩展
 
 ##lumen
 添加config/domain.php
 ```
<?php

return [
    'login_redirect_url_prod' => env('LOGIN_REDIRECT_URL_PROD', 'https://sso.jiumiaodai.com/sso/login'),
    'http_sso_check_prod' => env('HTTP_SSO_CHECK_PROD', 'https://sso.jiumiaodai.com/sso/token'),
    'cookie_clean_domain_prod' => env('COOKIE_CLEAN_DOMAIN_PROD', '.jiumiaodai.com'),
    'ticket_cookie_name_prod' => env('TICKET_COOKIE_NAME_PROD', 'ticket_prod'),
];

```