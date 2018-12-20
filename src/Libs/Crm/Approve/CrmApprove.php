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
    public static function cancel($orderId)
    {
        $request = new BaseRequest();
        $url = 'api/main/approve-cancel';
        $request->setUrl($url);

        $postData = [
            'orderId' => $orderId,
        ];
        $request->setData($postData);
        return $request->execute();
    }
}