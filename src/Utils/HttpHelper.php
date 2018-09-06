<?php

namespace JMD\Utils;

use JMD\App\Utils;

class HttpHelper
{

    public static function size($url)
    {
        $result = -1;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($curl, CURLOPT_USERAGENT, get_user_agent_string());
        $data = curl_exec($curl);
        curl_close($curl);
        if ($data) {
            if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
                return (int)$matches[1];
            }
        }
        return $result;
    }

    public static function curl($url, $post = null, $headers = [], $cookies = null)
    {
        if (!isset($headers['User-Agent'])) {
            $headers['User-Agent'] = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36';
        }
        if ($cookies) {
            $headers['Cookie'] = self::stringCookies($cookies);
        }
        foreach ($headers as $key => &$value) {
            if ($key) {
                $value = "$key: $value";
            }
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1); // 设置为POST方式
        }
        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, (http_build_query($post))); // POST数据
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_values($headers));

        $html = curl_exec($ch);
        if ($errorno = curl_errno($ch)) {
            // throw new \Exception(curl_error($ch) . json_encode(curl_getinfo($ch), JSON_UNESCAPED_UNICODE), $errorno);
            Utils::alert('php-libs请求异常',
                json_encode(
                    [
                        'url' => $url,
                        'content' => curl_getinfo($ch),
                        'callback' => curl_error($ch),
                        'data' => $post
                    ], 256));
        }


        curl_close($ch);
        // $info = curl_getinfo($ch);
        // print_r($info);
        return $html;
    }

    public static function get($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    public static function post($url, $data = null, $timeOut = 30)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, 1); // 设置为POST方式
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // POST数据
        }
        $html = curl_exec($ch);
        if ($errorno = curl_errno($ch)) {
            // throw new \Exception(curl_error($ch) . json_encode(curl_getinfo($ch), JSON_UNESCAPED_UNICODE), $errorno);
            Utils::alert('php-libs请求异常',
                json_encode(
                    [
                        'url' => $url,
                        'content' => curl_getinfo($ch),
                        'callback' => curl_error($ch),
                        'data' => $data
                    ], 256));
        }


        curl_close($ch);
        return $html;
    }

    public static function stringCookies($cookies)
    {
        if (is_string($cookies)) {
            return $cookies;
        }
        $str = '';
        foreach ($cookies as $cookie) {
            $str .= $cookie['name'] . '=' . $cookie['value'] . ';';
        }
        return $str;
    }
}