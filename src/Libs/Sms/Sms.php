<?php

namespace JMD\Libs\Sms;

use JMD\App\Configs;
use JMD\App\Utils;
use JMD\Common\sendKey;
use JMD\Libs\Sms\Interfaces\SmsBase;
use JMD\Libs\Sms\Tunnels\JPush;
use JMD\Libs\Sms\Tunnels\MontenNets;
use JMD\Libs\Sms\Tunnels\TianRuiYun;
use JMD\Libs\Sms\Tunnels\Winic;
use JMD\Libs\Sms\Tunnels\XingYunXiang;
use JMD\Libs\Sms\Tunnels\XuanWu;
use phpDocumentor\Reflection\Types\Self_;

/**
 * 短信入口
 * Class Sms
 * @package JMD\Libs\Sms
 */
class Sms implements sendKey
{
    //+--------------------------
    //|     短信渠道配置
    //| PS:从上到下发送。越靠前优先级越高，根据需求可调整。
    //| update_time：2017-09-28 14:04
    //+--------------------------
    private static $tunnels = [
        self::TUNNELS_CAPTCHA => [
            JPush::class,
            XuanWu::class,
            XingYunXiang::class,
            TianRuiYun::class,
//            Guodu::class,  // 已封停，等待官方解封
        ],
        self::TUNNELS_VOICE_CAPTCHA => [
            Winic::class
        ],
        self::TUNNELS_VOICE_NOTICE => [
            MontenNets::class
        ],
        self::TUNNELS_NOTICE => [
//            JPush::class,  // 模板更换等待审核完成
            XuanWu::class,
            TianRuiYun::class,
        ],
        # 营销渠道，按运营商通道发送
        self::TUNNELS_MARKETNG => [
            Utils::LIANTONG => [
                TianRuiYun::class,
                XingYunXiang::class,
            ],
            Utils::YIDONG => [
                TianRuiYun::class,
                XingYunXiang::class,
            ],
            Utils::DIANXIN => []
        ],
        # 自定义短信文案发送
        self::TUNNELS_CUSTOM => [
            XuanWu::class,
            TianRuiYun::class,
            XingYunXiang::class,
        ]
    ];

    //+-----------------------
    //|     短信通道配置
    //| PS:无需修改
    //| update_time:2017-09-28 14:04
    //+-----------------------
    const TUNNELS_CAPTCHA = 'sendCaptcha';
    const TUNNELS_VOICE_CAPTCHA = 'sendVoiceCaptcha';
    const TUNNELS_VOICE_NOTICE = 'sendVoiceNotice';
    const TUNNELS_NOTICE = 'sendNotice';
    const TUNNELS_MARKETNG = 'sendMarketing';
    const TUNNELS_CUSTOM = 'sendCustom';  //自定义文案

    //+-----------------------
    //| 系统配置，一般不需要修改
    //+-----------------------
    const SMS_CONFIG = 'smsConfig';
    const SMS_TUNNELS = 'smsTunnels';
    const TEXT_TEMPLATE = 'textTemplate';
    const TUNNELS_CONFIG = 'tunnels_config';
    const SEND_TUNNELS_CONFIG = 'send_tunnels_config';

    ########################  ☝上面是主要配置，☟以下是实现方法 #######################

    /**
     * 模板ID短信发送入口
     * @param $mobile
     * @param $sendKey
     * @param array $tplKey
     * @param array $tplParams
     * @param string $appName
     * @return bool
     */
    public static function send($mobile, $sendKey, $tplKey = [], $tplParams = [], $appName = '')
    {
        /** 测试环境不发送短信 */
        if (!Configs::isProEnv()) {
            return true;
        }

        $config = Utils::getParam(self::SMS_CONFIG);

        // 检查是否定义了模板ID
        if (!isset($config[self::TEXT_TEMPLATE][$sendKey])) {
            return false;
        }

        // 查询发送类型
        $tunnelType = $config[self::SMS_TUNNELS][$sendKey];
        if (!$tunnelType) {
            return false;
        }

        // 检查是否有可用的短信渠道
        if (empty(self::$tunnels[$tunnelType])) {
            return false;
        }

        switch ($tunnelType) {
            /**
             * 营销短信按运营商通道发送 通道
             */
            case self::TUNNELS_MARKETNG:

                $result = [];
                foreach (self::$tunnels[$tunnelType] as $key => $obj) {
                    #筛选号码【按运营商发送。因为有的渠道不支持某运营商】
                    $operator = self::filterTel($mobile);
                    #号码或者通道为空结束此次循环
                    if (empty($operator[$key]) || empty($obj)) {
                        continue;
                    }
                    $flag = false;
                    foreach ($obj as $item) {
                        #严格模式
                        if ($item instanceof SmsBase) {
                            return false;
                        }
                        #发送
                        $tunnel = new $item($operator[$key], $sendKey, $tplKey, $tplParams, $appName);
                        $flag = $tunnel->$tunnelType();
                        $result[] = $flag; // 记录结果
                        #成功直接结束循环，否则切换通道继续尝试发送
                        if ($flag == true) {
                            break;
                        } else {
                            Utils::alert('营销短信有运营商通道发送失败', json_encode([
                                '运营商' => $key,
                                '渠道' => $item,
                                'tel' => $mobile,
                                'sendKey' => $sendKey,
                                'tplKey' => $tplKey,
                                'tplParam' => $tplParams,
                                'flag' => $flag,
                                'send_tunnels' => self::$tunnels[$tunnelType]
                            ], 256));
                            continue;
                        }
                    }
                }

                // 判断发送结果
                if (!in_array(false, $result) && !empty($result)) {
                    return true;
                }
                break;

            /**
             * 验证码+通知类 通道
             */
            case self::TUNNELS_CAPTCHA:
            case self::TUNNELS_NOTICE:
//                $flag = false;
//                foreach (self::$tunnels[$tunnelType] as $obj) {
//                    #严格模式
//                    if ($obj instanceof SmsBase) {
//                        return false;
//                    }
//                    #发送
//                    $tunnel = new $obj($mobile, $sendKey, $tplKey, $tplParams, $appName);
//                    $flag = $tunnel->$tunnelType();
//                    #成功直接返回否则切换通道继续尝试发送
//                    if ($flag == true) {
//                        return true;
//                    }else{
//                        Utils::alert('【'.$obj.'】短信渠道发送失败，请检查，已自动切换备用渠道。', json_encode(['tel' => $mobile, 'sendKey' => $sendKey, 'tplKey' => $tplKey, 'tplParam' => $tplParams, 'flag' => $flag, 'send_tunnels' => self::$tunnels[$tunnelType]], 256));
//                    }
//                }

                $tunnelNum = count(self::$tunnels[$tunnelType]);

                // 获取缓存的通道初始序号
                $cacheKey = 'captcha-tunnel-' . date('-Ymd-') . $mobile;
                $tunnelIndex = intval(Utils::getCache($cacheKey)) % $tunnelNum;

                // 循环发送所有通道，直到成功或全部通道都失败
                $times = 0;
                $flag = false;
                do {
                    $className = self::$tunnels[$tunnelType][$tunnelIndex % $tunnelNum];
                    if (class_exists($className)) {
                        $tunnel = new $className($mobile, $sendKey, $tplKey, $tplParams, $appName);
                        $flag = $tunnel->$tunnelType();
                    } else {
                        EmailHelper::sendEmail('短信渠道异常', "tunnelType-{$tunnelType}/tunnelIndex-{$tunnelIndex}/appName-{$appName}");
                    }
                    $tunnelIndex++;
                    $times++;
                } while (!$flag && $times < $tunnelNum);

                Utils::setCache($cacheKey, $tunnelIndex % $tunnelNum, 60 * 60 * 24);
                if (!$flag) {
                    // 如果所有通道都false会走到这一步
                    Utils::alert('所有短信渠道发送失败', json_encode([
                        'tel' => $mobile,
                        'sendKey' => $sendKey,
                        'tplKey' => $tplKey,
                        'tplParam' => $tplParams,
                        'flag' => $flag,
                        'send_tunnels' => self::$tunnels[$tunnelType]
                    ], 256));
                } else {
                    return true;
                }
                break;
        }

        return false;
    }

    public static function sendVoiceCaptcha($mobile)
    {
        $tunnelType = self::TUNNELS_VOICE_CAPTCHA;
        $tunnels = self::$tunnels[$tunnelType];

        
        if (empty($tunnels)) {
            return false;
        }
        $tunnelNum = count(self::$tunnels[$tunnelType]);

        // 获取缓存的通道初始序号
        $cacheKey = 'voice-captcha-tunnel-' . date('-Ymd-') . $mobile;
        $tunnelIndex = intval(Utils::getCache($cacheKey)) % $tunnelNum;
        // 循环发送所有通道，直到成功或全部通道都失败
        $times = 0;
        $flag = false;
        do {
            $class = $tunnels[$tunnelIndex++];
            $tunnel = new $class($mobile);
            $flag = $tunnel->$tunnelType();
            $times++;
        } while (!$flag && $times < $tunnelNum);

        return $flag;
    }
    
    public static function sendVoiceByTpl($mobile,$key)
    {
        $tunnelType = self::TUNNELS_VOICE_NOTICE;
        $tunnels = self::$tunnels[$tunnelType];

        $config = Utils::getParam(self::SMS_CONFIG)[self::TUNNELS_CONFIG];
        if (empty($tunnels)) {
            return false;
        }

        $flag = false;
        foreach ($tunnels as $obj) {
            $tplid = $config[$obj::$configName][$key];
            if (!$tplid) {
                Utils::alert('语音推送模板不存在', $key);
            }
            $obj = new $obj($mobile);
            $flag = $obj->$tunnelType($tplid);
            #成功直接返回否则切换通道继续尝试发送
            if ($flag == true) {
                return true;
            }
        }

        // 如果所有通道都false会走到这一步
        Utils::alert('所有短信渠道发送失败',
            json_encode(['tel' => $mobile, 'flag' => $flag, 'send_tunnels' => $tunnelType],
                256));
        return false;
    }

    /**
     * 发送自定义短信文案文案
     * 配合可以使用自定义的文案的渠道使用
     * 针对深度定制或者临时发送的短信文案使用
     * @param array $mobile
     * @param $content
     * @param $appName
     * @return bool
     */
    public static function sendCustom($mobile = [], $content, $appName = '')
    {
        $tunnelType = self::TUNNELS_CUSTOM;
        $tunnels = self::$tunnels[$tunnelType];

        if (empty($tunnels)) {
            return false;
        }

        $flag = false;
        foreach ($tunnels as $obj) {
            #严格模式
            if ($obj instanceof SmsBase) {
                return false;
            }
            #发送
            $flag = $obj::$tunnelType($mobile, $content, $appName);
            #成功直接返回否则切换通道继续尝试发送
            if ($flag == true) {
                return true;
            }
        }

        // 如果所有通道都false会走到这一步
        Utils::alert('所有短信渠道发送失败',
            json_encode(['tel' => $mobile, 'content' => $content, 'flag' => $flag, 'send_tunnels' => $tunnelType],
                256));
        return false;
    }

    /**
     * 短信验证码生成规则
     * @param $captcha
     * @return string
     */
    public static function getRandomCaptcha($captcha)
    {
        if ($captcha) {
            return $captcha;
        }
        return sprintf('%04s', mt_rand(0, 9999));
    }

    /**
     * 根据手机运营商对手机号码进行分组
     * @param array|string|int $mobile
     * @return array
     */
    private static function filterTel($mobile = [])
    {
        # 如果不是字符串则转换
        if (!is_array($mobile)) {
            $mobile = explode(',', $mobile);
        }

        $telArr[Utils::DIANXIN] = [];
        $telArr[Utils::YIDONG] = [];
        $telArr[Utils::LIANTONG] = [];

        foreach ($mobile as $value) {
            switch (Utils::getOperator($value)) {
                case Utils::DIANXIN:
                    // TODO 电信运营商不允许发送营销短信，暂不做处理
                    $telArr[Utils::DIANXIN][] = $value;
                    break;
                case Utils::YIDONG:
                    // TODO 使用国都
                    $telArr[Utils::YIDONG][] = $value;
                    break;
                case Utils::LIANTONG:
                    $telArr[Utils::LIANTONG][] = $value;
                    break;
            }
        }

        return $telArr;
    }
}