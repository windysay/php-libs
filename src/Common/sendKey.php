<?php

namespace JMD\Common;

interface sendKey
{
    /**
     * 业务流程
     */
    const CAPTCHA = 'CAPTCHA';
    const LOANED_FAILED = 'LOANED_FAILED';
    const SYSTEM_ORDER_CANCEL = 'SYSTEM_ORDER_CANCEL';
    const TIME_TO_REPAYMENT = 'TIME_TO_REPAYMENT';
    const REPAYMENT_FAILED = 'REPAYMENT_FAILED';
    const OVERDUE_NOTICE = 'OVERDUE_NOTICE';
    const EXTEND_SUCCESS = 'EXTEND_SUCCESS';
    const REPAYMENT_FINISH = 'REPAYMENT_FINISH';
    const REPAYMENT_FINISH_ONE = 'REPAYMENT_FINISH_ONE';
    const REPAYMENT_FINISH_TWO = 'REPAYMENT_FINISH_TWO';
    const LOAN_SUCCESS = 'LOAN_SUCCESS';
    const APPLY_PASS = 'APPLY_PASS'; //初审通过
    const TIME_OUT_CANCEL_APPLY = 'TIME_OUT_CANCEL_APPLY'; //放款周期过长，系统自动取消借款申请
    const EDB_POLICY_NOTICE = 'EDB_POLICY_NOTICE'; //保单模板

    //+++++++++++++++++
    //| 白名单[营销渠道]
    //| 注意下载地址，渠道为白名单
    //+++++++++++++++++
    const WHITELIST_1 = 'WHITELIST_1';

    //+++++++++++++++++
    //| 召回短信[营销渠道]
    //+++++++++++++++++
    # 注册了不申请的召回
    const REG_NO_APPLY = 'REG_NO_APPLY';
    # 注册了四项验证没完善的
    const REG_NO_AUTH = 'REG_NO_AUTH';
    # 认证了不申请的召回
    const AUTH_NO_APPLY = 'AUTH_NO_APPLY';
    # 签约后取消用户召回
    const SIGN_CANCEL = 'SIGN_NO_APPLY';
    # 老用户召回
    const OLD_USER_RECALL = 'OLD_USER_RECALL';

    # 语音验证码即将到期提醒
    const VOICE_NOTICE_WILL_EXPIRED = 'VOICE_NOTICE_WILL_EXPIRED';

    # 语音验证码到期
    const VOICE_NOTICE_EXPIRED = 'VOICE_NOTICE_EXPIRED';
    const VOICE_NOTICE_OVERDUE = 'VOICE_NOTICE_OVERDUE';

    # 语音验证码放款成功
    const VOICE_NOTICE_LENDING_SUCCESS = 'VOICE_NOTICE_LENDING_SUCCESS';

    #
    const VOICE_NOTICE_BATCH_IVR = 'VOICE_NOTICE_BATCH_IVR';

    const VOICE_NOTICE_USER_RECALL = 'VOICE_NOTICE_USER_RECALL';
    #认证模板
    const VOICE_NOTICE_AUTH = 'VOICE_NOTICE_AUTH';

    #邀请申请借款模板
    const VOICE_NOTICE_INVITE_APPLY = 'VOICE_NOTICE_INVITE_APPLY';

    #订单创建召回
    const VOICE_NOTICE_ORDER_CREATE = 'VOICE_NOTICE_ORDER_CREATE';
    #订单确认召回
    const VOICE_NOTICE_ORDER_SYSTEM = 'VOICE_NOTICE_ORDER_SYSTEM';
    #订单完成召回
    const VOICE_NOTICE_ORDER_FINISH = 'VOICE_NOTICE_ORDER_FINISH';
    #邀请完善资料
    const VOICE_NOTICE_INFO_PERFACT = 'VOICE_NOTICE_INFO_PERFACT';
    #邀请完善资料
    const VOICE_NOTICE_INVITE_LOAN = 'VOICE_NOTICE_INVITE_LOAN';
    #购买会员营销
    const VOICE_NOTICE_BUY_MEMBER = 'VOICE_NOTICE_BUY_MEMBER';
    #新网通道切换取消用户召回
    const VOICE_NOTICE_XINWANG_FINISH = 'VOICE_NOTICE_XINWANG_FINISH';

    #渠道标识
    const CHANNEL_JPUSH = 'JPUSH';
    const CHANNEL_TIAN_RUI_YUN = 'TIAN_RUI_YUN';
    const CHANNEL_XING_YUN_XIANG = 'XING_YUN_XIANG';
    const CHANNEL_XUAN_WU = 'XUAN_WU';
    const CHANNEL_MENG_WANG = 'MENG_WANG';
    const CHANNEL_JQB_SERVICE = 'JQB_SERVICE';//微服务标识
}