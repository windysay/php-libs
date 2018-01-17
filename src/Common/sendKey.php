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
}