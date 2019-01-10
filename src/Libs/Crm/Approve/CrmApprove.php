<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 14:47
 */

namespace JMD\Libs\Crm\Approve;

use JMD\Libs\Crm\BaseRequest;

class CrmApprove
{
    /**
     * 用户取消/人工取消
     */
    const NORMAL_CANCEL = 1;

    /**
     * 定时任务取消
     */
    const CRONTAB_CANCEL = 2;

    public static function cancel($orderId, $type = self::NORMAL_CANCEL)
    {
        $request = new BaseRequest();
        $url = 'api/main/approve-cancel';
        $request->setUrl($url);

        $postData = [
            'orderId' => $orderId,
            'type' => $type
        ];
        $request->setData($postData);
        return $request->execute();
    }
}