<?php
return [
    //+-------------------
    //|短信渠道公共配置
    //|自定义变量格式为：{{变量名}}
    //|变量数组严格按照顺序排列
    //+---------------------
    \JMD\Libs\Sms\Sms::SMS_CONFIG => [
        # 指定短信发送通道
        \JMD\Libs\Sms\Sms::SMS_TUNNELS => $commonConfig[\JMD\Libs\Sms\Sms::SEND_TUNNELS_CONFIG],

        # 通用模板，针对可以发送自定义文本的短信渠道
        \JMD\Libs\Sms\Sms::TEXT_TEMPLATE => $commonConfig[\JMD\Libs\Sms\Sms::TEXT_TEMPLATE],

        # 短信发送渠道模板集合
        \JMD\Libs\Sms\Sms::TUNNELS_CONFIG => [
            /**
             * 极光
             */
            \JMD\Libs\Sms\Tunnels\JPush::$configName => [
                \JMD\Common\sendKey::CAPTCHA => 138124,
                \JMD\Common\sendKey::LOANED_FAILED => 139097,
                \JMD\Common\sendKey::SYSTEM_ORDER_CANCEL => 139097,
                \JMD\Common\sendKey::TIME_TO_REPAYMENT => 139144,
                \JMD\Common\sendKey::REPAYMENT_FAILED => 139185,
                \JMD\Common\sendKey::OVERDUE_NOTICE => 139127,
                \JMD\Common\sendKey::EXTEND_SUCCESS => 139121,
                \JMD\Common\sendKey::REPAYMENT_FINISH => 139120,
                \JMD\Common\sendKey::LOAN_SUCCESS => 139112,
                \JMD\Common\sendKey::APPLY_PASS => 148015,
            ],
            /**
             * 天瑞
             * 由于是一对多【一个模板对多个应用】故放到公共配置里
             */
            \JMD\Libs\Sms\Tunnels\TianRuiYun::$configName => $commonConfig[\JMD\Libs\Sms\Sms::TUNNELS_CONFIG][\JMD\Libs\Sms\Tunnels\TianRuiYun::$configName],

            /**
             * 玄武短信配置
             * 一个账号一个签名，这里存放对应账号，自动加载，其他app没有该配置则标识不支持
             */
            \JMD\Libs\Sms\Tunnels\XuanWu::$configName => [
                // 验证码+通知类
                \JMD\Libs\Sms\Tunnels\XuanWu::$captchaAndNoticeName => [
                    'username' => 'SZJQB@SZJQB',
                    'password' => '16FLv5To',
                    'msgtype' => 1,
                ],
                // 营销账号
                \JMD\Libs\Sms\Tunnels\XuanWu::$marketingConfigName => [
                    'username' => 'SZJQB1@SZJQB',
                    'password' => 'bLfAn6TC',
                    'msgtype' => 4,
                ]
            ],

            \JMD\Libs\Sms\Tunnels\MontenNets::$configName => $commonConfig[\JMD\Libs\Sms\Sms::TUNNELS_CONFIG][\JMD\Libs\Sms\Tunnels\MontenNets::$configName],
            \JMD\Libs\Sms\Tunnels\XuanWuVoice::$configName => $commonConfig[\JMD\Libs\Sms\Sms::TUNNELS_CONFIG][\JMD\Libs\Sms\Tunnels\XuanWuVoice::$configName],
        ],
    ]
];