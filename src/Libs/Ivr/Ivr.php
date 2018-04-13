<?php
/**
 * Created by PhpStorm.
 * User: Summer
 * Date: 2018/4/8
 * Time: 9:50
 */

namespace JMD\Libs\Ivr;

use common\components\jpush\JPush;
use common\components\wechat\WxTemplatePush;
use common\helpers\DateTime;
use common\jobs\ivr\IVRCall;
use common\models\ApproveLog;
use common\models\ApproveManualLog;
use common\models\Contract;
use common\models\IvrCallLog;
use common\models\Order;
use common\models\UserContact;
use JMD\Libs\Ivr\Interfaces\IvrInterface;
use common\models\OrderLog;
use Yii;

class Ivr
{

    // IVR_VALIDATE_USER_INFO:orderId,缓存用户的呼叫次数
    const REDIS_CACHE_KEY = 'IVR_VALIDATE_USER_INFO:';
    // 初审池的redis Key
    const REDIS_FIRST_POOL = 'IVR_FIRST_POOL:';
    // 用户IVR认证失败(IVR验证失败2次,外呼超过3次,订单改为待电审二审)
    const REDIS_IVR_CHECK_FAIL = 'IVR_CHECK_FAIL:';

    //用户接听电话
    const ANSWER_CALL_YES = 1;
    //用户拒听
    const ANSWER_CALL_NO = 2;

    //打电话给用户每次的间隔时间 (60*60) 1小时
    const WAIT_TO_CALL = 120;

    //设置7moor webcall 两次接口请求的最小时间,防止多次回调主要针对7moor webcall 回调两次
    const REDIS_INTERVAL_CALL_SECOND = 60;
    const REDIS_INTERVAL_CALL = 'IVR_INTERVAL_CALL:';

    /**
     * IVR 接口调起
     * @param IvrInterface $IvrConcrete
     * @param $phone
     * @param $orderId
     */
    public static function callUpIVR(IvrInterface $IvrConcrete, $phone, $orderId)
    {
        $IvrConcrete->send($phone, $orderId);
    }

    public static function manualPass(Order $order)
    {
        $redis = Ivr::redis_init();
        //延迟1小时,等待用户准备资料
        Yii::$app->mqueue->delay(Ivr::WAIT_TO_CALL)->push(new IVRCall($order->order_id, $order->user->telephone));
        //修改订单状态 IVR待电审状态
        Ivr::changeOrderStatus($order, Order::STATUS_WAIT_IVR_CALL);
        //记录当前订单的审批池的redis key
        $redisKey = Ivr::REDIS_FIRST_POOL . $order->order_id;
        $redis->set($redisKey, Yii::$app->user->id);
        $redis->expire($redisKey, 60 * 60 * 12);
    }

    /**
     * 处理 IVR回调
     * @param Order $order
     * @param $status
     */
    public static function handleIvrCallBack(Order $order, $status)
    {
        $callCount = self::incrCallNumber($order);

        //记录日志
        $ivrCallLog = IvrCallLog::findOne(['order_id' => $order->order_id, 'user_id' => $order->user_id]);
        if ($callCount == 1) {
            //创建日志
            $res = IvrCallLog::create([
                'order_id' => $order->order_id,
                'user_id' => $order->user_id,
                'status' => IvrCallLog::STATUS_CHECKING,
                'ivr_check_count' => 1,
            ]);
        } else {
            $ivrCallLog = IvrCallLog::findOne(['order_id' => $order->order_id, 'user_id' => $order->user_id]);
            if ($callCount == 2) {
                //修改订单状态 IVR电审呼出中
                self::changeOrderStatus($order, Order::STATUS_WAIT_IVR_CALLING);
            } elseif ($callCount >= 3 && $status == self::ANSWER_CALL_NO) {
                //大于三次呼叫,用户没有接听电话(呼叫三次以后直接电审二审(人工审核))
                //修改订单状态为41 等待人工电审
                $order->manualToCall();
                $ivrCallLog->error_message = '呼叫三次,直接电审二审(人工审核)';
                $ivrCallLog->status = IvrCallLog::STATUS_CHECK_FAIL;

                //清除redis缓存记录
                self::cleanCache($order->order_id);
            }

            $ivrCallLog->ivr_check_count = $callCount;
            $ivrCallLog->save();
        }

        //判断用户是否拒接,是否需要再次呼叫
        if ($callCount > 0 && $callCount < 3 && $status == self::ANSWER_CALL_NO) {
            /** @var yii\queue\db\Queue $mqueue */
            //一小时后再执行
            Yii::$app->mqueue->delay(self::WAIT_TO_CALL)->push(new IVRCall($order->order_id, $order->user->telephone));
        }
    }

    /**
     * IVR验证失败后判断是否需要再次呼叫用户
     * @param Order $order
     */
    public static function handleIvrCheckFail(Order $order, IvrCallLog $ivrCallLog)
    {
        $failCount = self::incrIvrCheckFailNumber($order);

        //IVR验证失败1次,更改订单状态为IVR验证一次失败
        if ($failCount == 1) {
            self::changeOrderStatus($order, Order::STATUS_IVR_CALLING_FAIL_ONE);
        }

        if ($failCount >= 2) {
            //IVR验证失败两次,订单状态改为待电审二审(人工电审)
            $order->manualToCall();

            $ivrCallLog->status = IvrCallLog::STATUS_IVR_CALLING_FAIL_TWO;
            $ivrCallLog->error_message = 'IVR验证失败两次,订单状态改为待电审二审(人工电审)';

            //清除redis缓存记录
            self::cleanCache($order->order_id);

        } else {
            //重新呼叫用户验证信息
            /** @var yii\queue\db\Queue $mqueue */
            //一小时后再执行
            Yii::$app->mqueue->delay(self::WAIT_TO_CALL)->push(new IVRCall($order->order_id, $order->user->telephone));

            $ivrCallLog->status = IvrCallLog::STATUS_IVR_CALLING_FAIL_ONE;
            $ivrCallLog->error_message = 'IVR验证失败一次,再次呼叫用户';

            //推送消息
            self::pushMessage(Order::STATUS_IVR_CALLING_FAIL_ONE, $order);
        }

        $ivrCallLog->save();
    }

    /*
     * 增加呼叫次数
     */
    public static function incrCallNumber(Order $order)
    {
        $redis = self::redis_init();
        $redisKey = self::REDIS_CACHE_KEY . $order->order_id;

        $callCount = $redis->get($redisKey);
        if (empty($callCount)) {
            //第一次呼叫
            $redis->set($redisKey, 1);
            //数据缓存一天
            $redis->expire($redisKey, 60 * 60 * 12);
        } else {
            $redis->incr($redisKey);
        }

        $redis->set(self::REDIS_INTERVAL_CALL . $order->order_id, self::REDIS_INTERVAL_CALL_SECOND + time());
        $redis->expire(self::REDIS_INTERVAL_CALL . $order->order_id, 60 * 60 * 3);

        return $redis->get($redisKey);
    }

    /**
     * 验证IVR审核次数(接听电话以后,用户验证信息失败)
     */
    public static function incrIvrCheckFailNumber(Order $order)
    {
        $redis = self::redis_init();
        $redisKey = self::REDIS_IVR_CHECK_FAIL . $order->order_id;

        $callIvrCount = $redis->get($redisKey);
        if (empty($callIvrCount)) {
            //第一次错误
            $redis->set($redisKey, 1);
            $redis->expire($redisKey, 60 * 60 * 12);
        } else {
            $redis->incr($redisKey);
        }

        return $redis->get($redisKey);
    }

    public static function changeOrderStatus(Order $order, $status, $orderLogName = OrderLog::IVR_TO_CALL)
    {
        Order::getDb()->transaction(function ($db) use ($order, $status, $orderLogName) {
            $fromStatus = $order->getOldAttribute('status');
            $order->status = $status;
            $order->manual_time = DateTime::timeMs();
            $order->save(false);
            OrderLog::log($order->user_id, $order->order_id, $orderLogName, $fromStatus, $order->status, $order->principal);
        });

    }

    public static function verifyUserInfo(Order $order, $idCardNo, $phoneNo)
    {

        //获取银行卡号
        $originalIdCard = $order->user->id_card_no;
        //获取紧急联系人
        $originalContacts = UserContact::find()
            ->select(['contact_telephone'])
            ->where(['user_id' => $order->user_id, 'status' => UserContact::STATUS_ACTIVE])
            ->asArray()
            ->all();
        $lastSix = substr($originalIdCard, -6);
        $originalContacts = array_column($originalContacts, 'contact_telephone');

        //保存用户输入信息
        $ivrCallLog = IvrCallLog::findOne(['order_id' => $order->order_id, 'user_id' => $order->user_id]);
        $ivrCallLog->u_id_card_no = $idCardNo;
        $ivrCallLog->sys_id_card_no = $originalIdCard;
        $ivrCallLog->u_emergency_phone = $phoneNo;
        $ivrCallLog->sys_emergency_phone = implode(',', $originalContacts);

        if ($lastSix == $idCardNo && in_array($phoneNo, $originalContacts)) {
            //VIR审核通过
            self::ivrCheckPass($order);

            $ivrCallLog->id_card_pass = 1;
            $ivrCallLog->phone_pass = 1;
            $ivrCallLog->status = 1;
            $ivrCallLog->save();

            //清除redis缓存记录
            self::cleanCache($order->order_id);

        } else {

            if ($lastSix == $idCardNo) {
                $ivrCallLog->id_card_pass = 1;
            } else {
                $ivrCallLog->id_card_pass = 0;
            }

            if (in_array($phoneNo, $originalContacts)) {
                $ivrCallLog->phone_pass = 1;
            } else {
                $ivrCallLog->phone_pass = 0;
            }

            //验证失败.查看用户IVR验证失败次数,是否需要再次呼叫
            self::handleIvrCheckFail($order, $ivrCallLog);

        }

    }

    /**
     * IVR 电审通过以后,需要执行的流程
     */
    public static function ivrCheckPass(Order $order)
    {

        $redis = self::redis_init();
        $userId = self::REDIS_FIRST_POOL . $order->order_id;
        // 防止重复审批
        if (in_array($order->status, [Order::STATUS_MANUAL_PASSED, Order::STATUS_MANUAL_REJECTED, Order::STATUS_WAIT_PAY, Order::STATUS_MANUAL_CANCELED])) {
            //删除当前审批池订单
            ApproveLog::del_own_approveList($redis->get($userId), $order->order_id);
            throw new \Exception('IVR@ivrCheckPass 该订单已审批,订单重复审批.订单ID:' . $order->order_id);
        }

        // 防止审批已生成合同订单
        if (isset($order->contract->contract_id)) {
            //删除当前审批池订单
            ApproveLog::del_own_approveList($redis->get($userId), $order->order_id);
            throw new \Exception('IVR@ivrCheckPass 合同已经生成, 不能进行审批操作.订单ID:' . $order->order_id);
        }

        //更新用户标签
        ApproveManualLog::updateUserinfo($order->user_id, ['is_emergency_contact' => 1]);

        //验证当前用户身份证是否有关联中的借款单
        if (Contract::hasNotCompleteContractByIdCardNo($order->user->id_card_no)) {
            //删除当前审批池订单
            ApproveLog::del_own_approveList($redis->get($userId), $order->order_id);
            throw new \Exception(' IVR@ivrCheckPass 当前用户关联账户中有未完成合同，暂不允许通过. 订单ID:' . $order->order_id);
        }

        $redis->del($userId);

        //审核通过
        $order->manualPass(0, '');

        //检查当前订单状态,上面的{$order->manualPass()}会更改订单状态
        if ($order->status == Order::STATUS_MANUAL_PASSED) {
            //推送消息
            self::pushMessage(Order::STATUS_MANUAL_PASSED, $order);
        }
    }

    /**
     * 根据订单状态给用户推送消息
     * @param $status
     * @param Order $order
     */
    public static function pushMessage($status, Order $order)
    {
        //客户端APP推送、微信推送
        if ($status == Order::STATUS_MANUAL_REJECTED) {
            //人工拒绝
            JPush::pushAndMessage('抱歉，审核未通过',
                '您的信息评估暂未通过，' . \Yii::$app->params['app_nickname'] . '推荐您申请其他贷款产品，详情点击“贷款救急”', $order->user_id, [],
                'all', 1);
            WxTemplatePush::unApproved($order);
        } elseif ($status == Order::STATUS_MANUAL_PASSED) {
            //人工通过
            JPush::pushAndMessage('恭喜您，审核通过', '您的资料已审核通过，请耐心等待下款。', $order->user_id);
            WxTemplatePush::LoanSubmitSuccess($order);
        } elseif ($status == Order::STATUS_WAIT_IVR_CALL) {
            //发送消息让用户准备资料
            JPush::pushAndMessage('审核已提交', '您的借款申请已提交审核，请准备好您的身份证号、紧急联系人号码，保持手机畅通，请注意接听3655开头的电话！', $order->user_id);
        } elseif ($status == Order::STATUS_IVR_CALLING_FAIL_ONE) {
            //IVR验证一次失败
            JPush::pushAndMessage('信息效验失败', '您的信息效验失败，请再次准备好您的身份证号、紧急联系人号码，保持手机畅通，请注意接听3655开头的电话！', $order->user_id);
        }
    }


    /**
     * @return Yii\redis\Connection
     */
    public static function redis_init()
    {
        /** @var yii\redis\Connection $redis */
        $redis = Yii::$app->redis;
        $redis->executeCommand('select', [0]);
        return $redis;
    }

    /**
     * 清除缓存记录
     */
    public static function cleanCache($orderId)
    {
        $redis = self::redis_init();
        $redis->del(self::REDIS_CACHE_KEY . $orderId, self::REDIS_FIRST_POOL . $orderId, self::REDIS_IVR_CHECK_FAIL . $orderId);
    }
}