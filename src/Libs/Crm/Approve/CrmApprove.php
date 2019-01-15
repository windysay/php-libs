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
     * 人工取消
     */
    const MANUAL_CANCEL = 1;

    /**
     * 定时任务取消
     */
    const CRONTAB_CANCEL = 2;

    /**
     * 用户取消
     */
    const USER_CANCEL = 3;

    /**
     * @param $orderId
     * @param $type
     * @return \JMD\Common\DataFormat
     * @throws \Exception
     */
    public static function cancel($orderId, $type = self::MANUAL_CANCEL)
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
