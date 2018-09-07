## 微服务接入文档
- 接入步骤
    - 在微服务后台申请AppKey&AppSecret
    - 安装php-libs组件(需要配置jmd-backend/php-libs个人git权限 才能拉取代码)
    ````
    ##composer.json配置repositories
    "repositories": [
        {
          "type": "git",
          "url": "ssh://git@code.aliyun.com/jmd-backend/php-libs.git"
        }
    ],
    composer require "jmd-backend/php-libs:dev-master"
    ````
    - 将申请的AppKey&AppSecret配置到config/domain.php
    ````
    //微服务公共配置
    'public_services_config' => [
        'app_key' => env('SERVICES_APP_KEY', 'xxxxx'),
        'app_secret_key' => env('SERVICES_APP_SECRET_KEY', 'xxxxxx'),
        'endpoint' => env('SERVICES_ENDPOINT', 'http://services.jiumiaodai.com/'), //测试域名http://api.services.dev23.jiumiaodai.com/
    ]
    ````
    - 即可接入对应的微服务

- 微服务接口说明 (建议:如果微服务请求失败,能自动切换到原有的服务处理 异常邮件统一发送到develop-alert@jiumiaodai.com)
    - 邮件微服务
        - 接入示例
        ````
        try {
            JMD::init(['projectType' => 'lumen']);
            $res = EmailServers::sendEmail(
                $content, //string|array邮件内容
                $title, //string 标题
                $sendTo, //string|array 接收人
                $sendFrom //string 发送人
            );
            if ($res->isSuccess()) {
                return true;
            }
            Email::send($res->getMsg(), 'xx项目邮件微服务发送失败 - ' . app()->environment());
        } catch (\Exception $e) {
            Email::send($e->getMessage(), 'xx项目邮件微服务访问异常 - ' . app()->environment());
        }
        ````
    - 异常微服务
        - 接入示例
        ````
        try {
            JMD::init(['projectType' => 'lumen']);
            $res = ExceptionServices::send(
                $exception, //string|array异常内容
                $receiver, //string|array异常通知接收人
            );
            if ($res->isSuccess()) {
                return;
            }
            Email::send($res->getMsg(), 'xx项目异常微服务发送失败 - ' . app()->environment());
        } catch (\Exception $e) {
            Email::send($e->getMessage(), 'xx项目异常微服务访问异常 - ' . app()->environment());
        }
        ````
    - 单点登录微服务(仅能接入正式)
        - 接入示例 - 获取用户信息
        ````
        /*
            $ticket ticket值
            $ip 访问用户ip
            $actionId 路由
        */
        try {
            JMD::init(['projectType' => 'lumen']);
            $res = SsoService::httpSsoCheck($ticket, $ip, $actionId);
            if ($res && $res->isSuccess()) {
                return $res->getData();
            } else {
                Email::send($result->getMsg(), 'xx项目单点登录微服务访问异常 - ' . app()->environment());
                return false;
            }
        } catch (\Exception $e) {
            Email::send($e->getMessage(), 'xx项目单点登录微服务访问异常 - ' . app()->environment());
        }
        ````
        - 前端 - 跳转地址
            - redirect_uri 登录成功回跳地址 
        ````
        https://sso.jiumiaodai.com/sso/login?redirect_uri=        
        ````
    - 短信微服务(升级事件短信中)
    - 统一RBAC(开发中)
    - 文件微服务(规划中)