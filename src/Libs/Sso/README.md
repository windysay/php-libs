## sso

#安装使用
 - 1.必须安装 composer require guzzlehttp/guzzle 组件
 - 2.必须安装 redis扩展
 
 ##lumen
 添加config/domain.php
 ```
<?php

return [
    'login_redirect_url_prod' => env('LOGIN_REDIRECT_URL_PROD', 'smtp'),
    'http_sso_check_prod' => env('HTTP_SSO_CHECK_PROD', 'smtp.mxhichina.com'),
    'cookie_clean_domain_prod' => env('COOKIE_CLEAN_DOMAIN_PROD', 465),
    'ticket_cookie_name_prod' => ['address' => 'TICKET_COOKIE_NAME_PROD','name' => '发件人']
];

```