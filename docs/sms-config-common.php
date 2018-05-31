<?php
return $commonConfig = [

    /**
     * 指定发送通道
     * 【公共配置】
     */
    \JMD\Libs\Sms\Sms::SEND_TUNNELS_CONFIG => [
        \JMD\Common\sendKey::CAPTCHA => \JMD\Libs\Sms\Sms::TUNNELS_CAPTCHA,
        \JMD\Common\sendKey::LOANED_FAILED => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::SYSTEM_ORDER_CANCEL => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::TIME_TO_REPAYMENT => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::REPAYMENT_FAILED => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::OVERDUE_NOTICE => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::EXTEND_SUCCESS => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::REPAYMENT_FINISH => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::LOAN_SUCCESS => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::WHITELIST_1 => \JMD\Libs\Sms\Sms::TUNNELS_MARKETNG,
        \JMD\Common\sendKey::REG_NO_APPLY => \JMD\Libs\Sms\Sms::TUNNELS_MARKETNG,
        \JMD\Common\sendKey::AUTH_NO_APPLY => \JMD\Libs\Sms\Sms::TUNNELS_MARKETNG,
        \JMD\Common\sendKey::SIGN_CANCEL => \JMD\Libs\Sms\Sms::TUNNELS_MARKETNG,
        \JMD\Common\sendKey::OLD_USER_RECALL => \JMD\Libs\Sms\Sms::TUNNELS_MARKETNG,
        \JMD\Common\sendKey::APPLY_PASS => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
        \JMD\Common\sendKey::TIME_OUT_CANCEL_APPLY => \JMD\Libs\Sms\Sms::TUNNELS_NOTICE,
    ],


    /**
     *|通用自定短信文案,针对使用自定义文案的短信渠道
     */
    \JMD\Libs\Sms\Sms::TEXT_TEMPLATE => [
        \JMD\Common\sendKey::CAPTCHA => '验证码：{{code}}。请勿告知他人，谨防上当受骗。温馨提示：{{app_name}}未授权任何个人或机构代客户申请，或收取前期费用',
        \JMD\Common\sendKey::LOANED_FAILED => '由于打款失败，您的订单{{order_no}} 已取消，请及时更换银行卡，不便之处敬请原谅！',
        \JMD\Common\sendKey::SYSTEM_ORDER_CANCEL => '由于系统升级导致当前订单失效，如需继续借款，请前往{{app_name}}APP重新提交订单，给您带来的不便敬请谅解！',
//        \JMD\Common\sendKey::TIME_TO_REPAYMENT => '尊敬的{{fullname}}，您于{{loan_time}}在{{app_name}}借款{{principal}}元，明天（{{appointment_day}}）是到期还款日。我们将从您尾号为{{bank_no}}的卡上扣除人民币{{repayment_amount}}元，请您务必保证银行卡中余额充足，以免还款逾期产生罚息。',
        \JMD\Common\sendKey::TIME_TO_REPAYMENT => '尊敬的{{fullname}}，明天是到期还款日。将从您尾号{{bank_no}}的卡上扣除人民币{{repayment_amount}}元，请保证卡中余额充足，避免逾期，感谢信任。',
//        \JMD\Common\sendKey::REPAYMENT_FAILED => '{{money}}元扣款失败，直接在{{app_name}}APP按照提示就可以自助还款！您也可以通过支付宝主动还款，备注姓名和手机号。<支付宝：jiumiaodai@163.com(刘凤)>。如有疑问，直接拨打{{telephone}}咨询。',
//        \JMD\Common\sendKey::REPAYMENT_FAILED => '{{money}}元费用扣款失败，可APP内还款或支付宝<jiumiaodai@163.com刘凤>备注姓名+手机号还款',
        \JMD\Common\sendKey::REPAYMENT_FAILED => '{{money}}元扣款失败，请登录九秒贷使用【银行卡支付】重新支付。或添加支付宝<jiumiaodai@163.com 刘凤>，备注“姓名+手机号”还款',
//        \JMD\Common\sendKey::OVERDUE_NOTICE => '{{fullname}}您好，{{app_nickname}}善意提醒，您{{principal}}元借款已经逾期{{overdays}}天，今天应还款金额为{{repayment_amount}}元，直接在{{app_name}}APP按照提示就可以自助还款！您也可以通过支付宝主动还款，备注姓名和手机号。<支付宝：jiumiaodai@163.com（刘凤）>。如有疑问，直接拨打{{telephone}}咨询。',
        \JMD\Common\sendKey::OVERDUE_NOTICE => '您的借款已逾期{{overdays}}天，应还{{repayment_amount}}元，可APP内还款或支付宝<jiumiaodai@163.com刘凤>备注姓名+手机号还款',
        \JMD\Common\sendKey::EXTEND_SUCCESS => '尊敬的{{fullname}}您的费用{{money}}元，已经结算。如有疑问，直接拨打{{telephone}}咨询。',
//        \JMD\Common\sendKey::REPAYMENT_FINISH => '尊敬的{{fullname}}您好，我们已收到您的还款{{money}}元，如有疑问，请给{{wx_name}}微信号留言，或直接拨打{{telephone}}咨询。感谢您对{{app_name}}的信任。',
        \JMD\Common\sendKey::REPAYMENT_FINISH => '尊敬的{{fullname}}，已收到您的还款{{money}}元，如有疑问请留言{{wx_name}}微信号，或拨{{telephone}}咨询。感谢您的信任',
        \JMD\Common\sendKey::LOAN_SUCCESS => '尊敬的{{fullname}}，您{{date}}在{{app_name}}借款{{money}}元已汇至您的（{{bank_card}}）银行卡，请留意银行到账短信。',
        \JMD\Common\sendKey::WHITELIST_1 => '您已获得{{money}}元内极速贷款资格，秒到账，不看征信！快速拿钱 {{url}} ',
//        \JMD\Common\sendKey::REG_NO_APPLY => '恭喜！您已获得超低利息特权，{{money}}额度，{{time}}分钟极速放款，快来登录激活立即拿钱 {{url}}',
        \JMD\Common\sendKey::REG_NO_APPLY => '恭喜！您已获得超低利息特权，有{{money}}信用可以领取！点击 {{url}} 激活额度，立即拿钱',
//        \JMD\Common\sendKey::REG_NO_AUTH => '尊敬的用户，您的优先审核资格还有1天过期，请尽快完成认证！点击 {{url}} 登录可领10元低息券',
        \JMD\Common\sendKey::REG_NO_AUTH => '您的优先审核资格将在1天内过期，还差一步立即拿钱！点击 {{url}} 申请借款，当天到账',
//        \JMD\Common\sendKey::OLD_USER_RECALL => '恭喜！您已获得超低利息特权，{{money}}额度，{{time}}分钟极速放款，快来登录激活立即拿钱 {{url}}',
        \JMD\Common\sendKey::OLD_USER_RECALL => '恭喜！您已获得超低利息特权，有{{money}}信用可以领取！点击 {{url}}激活额度，立即拿钱',
//        \JMD\Common\sendKey::AUTH_NO_APPLY => '您的信用可借{{money}}元现金，{{time}}分钟下款，{{day}}天内有效，点击领取抵用券 {{url}} 立即拿钱',
        \JMD\Common\sendKey::AUTH_NO_APPLY => '您有{{money}}信用可以领取，当天到账，请在24小时内登录 {{url}} 领取， 逾期自动取消',
//        \JMD\Common\sendKey::SIGN_CANCEL => '你已获得一笔{{money}}元额度贷款，还有{{num}}张息费抵用券，请速领取！请戳 {{url}} 逾期自动取消。',
        \JMD\Common\sendKey::SIGN_CANCEL => '尊敬的会员：您信用良好，已获{{money}}元贷款资格！快速审核，当天到账。点击 {{url}} 领取额度',
        \JMD\Common\sendKey::APPLY_PASS => '尊敬的用户您的借款申请已提交审核，请保持手机畅通，注意接听028开头的电话！',
        \JMD\Common\sendKey::TIME_OUT_CANCEL_APPLY => '由于您非会员用户，等待放款周期过长，系统已自动帮您取消借款申请。购买会员可优先放款噢！重新提交申请 http://t.cn/RpGws3C ',
    ],

    /**
     * 公共的短信渠道模板
     * 针对可以修改签名的短信渠道，模板不变。
     */
    \JMD\Libs\Sms\Sms::TUNNELS_CONFIG => [
        /**
         * 天瑞
         */
        \JMD\Libs\Sms\Tunnels\TianRuiYun::$configName => [
            \JMD\Common\sendKey::CAPTCHA => 1004,
            \JMD\Common\sendKey::LOANED_FAILED => 1006,
            \JMD\Common\sendKey::SYSTEM_ORDER_CANCEL => 1005,
            \JMD\Common\sendKey::TIME_TO_REPAYMENT => 1008,
            \JMD\Common\sendKey::REPAYMENT_FAILED => false,
            \JMD\Common\sendKey::OVERDUE_NOTICE => false,
            \JMD\Common\sendKey::EXTEND_SUCCESS => 1011,
            \JMD\Common\sendKey::REPAYMENT_FINISH => 1012,//1012
            \JMD\Common\sendKey::LOAN_SUCCESS => 1017,
            \JMD\Common\sendKey::WHITELIST_1 => 1493,
            \JMD\Common\sendKey::APPLY_PASS => 3499,
        ],
        \JMD\Libs\Sms\Tunnels\MontenNets::$configName => [
            \JMD\Common\sendKey::VOICE_NOTICE_EXPIRED => 200291, //到期语音通知模板
            \JMD\Common\sendKey::VOICE_NOTICE_WILL_EXPIRED => 200292,
            \JMD\Common\sendKey::VOICE_NOTICE_OVERDUE => 200322, //逾期语音通知模板
            \JMD\Common\sendKey::VOICE_NOTICE_LENDING_SUCCESS => 200340, //放款成功语音通知模版
            \JMD\Common\sendKey::VOICE_NOTICE_BATCH_IVR => 200342, //批量ivr

            \JMD\Common\sendKey::VOICE_NOTICE_USER_RECALL => 200345, //召回用户
            \JMD\Common\sendKey::VOICE_NOTICE_INVITE_APPLY => 200346, //批量推送认证邀请

            \JMD\Common\sendKey::VOICE_NOTICE_ORDER_CREATE => 200347, //订单创建召回
            \JMD\Common\sendKey::VOICE_NOTICE_ORDER_SYSTEM => 200348, //订单确认召回
            \JMD\Common\sendKey::VOICE_NOTICE_ORDER_FINISH => 200349, //订单完成召回

            \JMD\Common\sendKey::VOICE_NOTICE_INFO_PERFACT => 200357, //完善资料召回
            \JMD\Common\sendKey::VOICE_NOTICE_INVITE_LOAN => 200358, //完善资料召回

            \JMD\Common\sendKey::VOICE_NOTICE_BUY_MEMBER => 200401,//购买会员营销
            \JMD\Common\sendKey::VOICE_NOTICE_XINWANG_FINISH => 200426, //新网通道切换取消用户召回
        ],
        \JMD\Libs\Sms\Tunnels\XuanWuVoice::$configName => [
            \JMD\Common\sendKey::VOICE_NOTICE_EXPIRED => 20450, //到期语音通知模板
            \JMD\Common\sendKey::VOICE_NOTICE_WILL_EXPIRED => 20451,
            \JMD\Common\sendKey::VOICE_NOTICE_OVERDUE => 20452, //逾期语音通知模板
            \JMD\Common\sendKey::VOICE_NOTICE_LENDING_SUCCESS => 20453, //放款成功语音通知模版
            \JMD\Common\sendKey::VOICE_NOTICE_BATCH_IVR => 20454, //批量ivr

            \JMD\Common\sendKey::VOICE_NOTICE_USER_RECALL => 20455, //召回用户
            \JMD\Common\sendKey::VOICE_NOTICE_INVITE_APPLY => 20456, //批量推送认证邀请

            \JMD\Common\sendKey::VOICE_NOTICE_ORDER_CREATE => 20457, //订单创建召回
            \JMD\Common\sendKey::VOICE_NOTICE_ORDER_SYSTEM => 20458, //订单确认召回
            \JMD\Common\sendKey::VOICE_NOTICE_ORDER_FINISH => 20459, //订单完成召回

            \JMD\Common\sendKey::VOICE_NOTICE_INFO_PERFACT => 20460, //完善资料召回
            \JMD\Common\sendKey::VOICE_NOTICE_INVITE_LOAN => 20461, //完善资料召回

            \JMD\Common\sendKey::VOICE_NOTICE_BUY_MEMBER => 20462, //购买会员营销
            \JMD\Common\sendKey::VOICE_NOTICE_XINWANG_FINISH => 20565, //新网通道切换取消用户召回
        ]
    ],

];