<?php

namespace JMD\App;

use JMD\Libs\Sms\Sms;

/**
 * Class Utils
 * @package JMD\App
 */
class Utils extends BaseClass
{

    const DIANXIN = 'dianxin';
    const LIANTONG = 'liantong';
    const YIDONG = 'yidong';

    public static $class = 'Utils';

    /**
     * 模板内容替换
     * @param $text
     * @param $params
     * @return bool|mixed
     */
    public static function textReplace($text, $params = [])
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $text = str_replace("{{" . $key . "}}", $value, $text);
            }
            return $text;
        }
        return false;
    }

    /**
     * 判断手机运营商
     * 移动：134|135|136|137|138|139|147|150|151|152|157|158|159|187|188|178
     * 联通：130|131|132|155|156|185|186|176
     * 电信：133|153|180|181|189|177|173（1349卫通）
     */
    public static function getOperator($telephone)
    {

        if (preg_match("/^(133|153|180|181|189|177|173)\d{8}$/", $telephone)) {
            return self::DIANXIN;
        } elseif (preg_match("/^(130|131|132|155|156|185|186|176)\d{8}$/", $telephone)) {
            return self::LIANTONG;
        } elseif (preg_match("/^(134|135|136|137|138|139|147|150|151|152|157|158|159|187|188|178)\d{8}$/",
            $telephone)) {
            return self::YIDONG;
        }

        return false;
    }

    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    public static function array_to_object($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)self::array_to_object($v);
            }
        }

        return (object)$arr;
    }

    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    public static function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::object_to_array($v);
            }
        }

        return $obj;
    }

    /**
     * @param $data
     * @param $url
     * @param array $header
     * @param int $timeout
     * @return mixed
     */
    public static function curlPost($data, $url, $header = [], $timeout = 60)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //不自动输出任何内容到浏览器
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 设置header头
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function curlGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, false);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }


    /**
     * @param $response
     * @return \SimpleXMLElement
     */
    public static function parseResponseAsSimpleXmlElement($response)
    {
        // $response are encoded in 'GBK', so we'll have to convert that into 'UTF-8'
        // And there is another thing: simplexml_load_string will try to read the encoding declared in the xml
        // and generate a warning if that encoding is not supported, for example:
        //  warning: simplexml_load_string(): Entity: line 1: parser error : Unsupported encoding GBK

        $encoding = null;
        $response = preg_replace_callback('/(<\?xml.+?encoding\s*=\s*["\'])([^"\']+)(.+\?>)/',
            function ($matches) use (&$encoding) {
                // $matches[1] is the encoding, something like 'GBK', 'gbk'
                $encoding = strtolower($matches[2]);

                // replace the encoding so that simplexml_load_string() can work
                return $matches[1] . 'utf-8' . $matches[3];
            }, $response, 1);

        // convert the encoding
        if (!empty($encoding)) {
            $response = mb_convert_encoding($response, $encoding, 'utf-8');
        }

        return simplexml_load_string($response);
    }

    /**
     * 根据短信渠道和发送类型查找模板ID
     * @param $tunnelKey
     * @param $sendKey
     * @return bool
     */
    public static function getTemplateIdByKey($tunnelKey, $sendKey)
    {
        $config = Utils::getParam(Sms::SMS_CONFIG);
        if (isset($config[Sms::TUNNELS_CONFIG][$tunnelKey][$sendKey])) {
            return $config[Sms::TUNNELS_CONFIG][$tunnelKey][$sendKey];
        }
        return false;
    }

    /**
     * 查询通用短信模板
     * @param $tunnel
     * @return mixed
     */
    public static function getTextTemplate($sendKey, $params)
    {
        $template = Utils::getParam(Sms::SMS_CONFIG)[Sms::TEXT_TEMPLATE];
        if (!isset($template[$sendKey])) {
            return false;
        }
        return self::textReplace($template[$sendKey], $params);
    }

    /**
     * 数组对照返回
     * @param $key
     * @param array $keyArr
     * @param array $valArr
     * @return bool|mixed
     */
    public static function getKeyToKey($keyArr = [], $valArr = [], $key = '')
    {
        $arr = [];
        foreach ($keyArr as $k => $v) {
            $arr[$v] = $valArr[$k];
        }

        if ($key) {
            if (!isset($arr[$key])) {
                return false;
            }
            return $arr[$key];
        }
        return $arr;
    }

    /**
     * 短信运营商规定 >70<=134 个字消耗2条短信  >134<=210消耗3条短信
     */
    public static function smsTextStrlen($text, $channel)
    {
        // 检测文案是否消耗两条短信
        $strlen = mb_strlen($text);
        if ($strlen > 70 && $strlen < 134) {
//            Utils::alert('发现[' . $channel . ']消耗短信条数等于【2】的文案->' . $mobile, $text, 'wukunqin@jiumiaodai.com');
        }
        // 检测文案是否消耗三条短信
        if ($strlen > 134) {
            Utils::alert('发现[' . $channel . ']消耗短信条数等于【3】的文案->', $text);
        }

        return true;
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return bool|string
     * @throws \Exception
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * 获取一级域名
     *
     * @return string
     */
    public static function getHost(){
        $url   = $_SERVER['HTTP_HOST'];
        $data = explode('.', $url);
        $co_ta = count($data);
        //判断是否是双后缀
        $zi_tow = true;
        $host_cn = 'com.cn,net.cn,org.cn,gov.cn';
        $host_cn = explode(',', $host_cn);
        foreach($host_cn as $host){
            if(strpos($url,$host)){
                $zi_tow = false;
            }
        }
        //如果是返回FALSE ，如果不是返回true
        if($zi_tow == true){
            $host = $data[$co_ta-2].'.'.$data[$co_ta-1];
        }else{
            $host = $data[$co_ta-3].'.'.$data[$co_ta-2].'.'.$data[$co_ta-1];
        }
        //兼容IP
        if(is_numeric($data[$co_ta-2]) && $data[$co_ta-2] < 256){
            $host = $data[$co_ta-4].'.'.$data[$co_ta-3].'.'.$data[$co_ta-2].'.'.explode(':', $data[$co_ta-1])[0];
        }
        return $host;
    }

}