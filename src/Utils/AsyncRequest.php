<?php

namespace JMD\Utils;

/**
 * Class AsyncRequest 异步请求
 * @package JMD\Utils
 */
class AsyncRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    public static function get($url, $params = [])
    {
        self::exec(self::METHOD_GET, $url, $params);
    }

    public static function post($url, $params = [])
    {
        self::exec(self::METHOD_POST, $url, $params);
    }

    private static function exec($method, $url, $params = [])
    {
        $url_info = parse_url($url);
        $host = $url_info['host'];
        $port = isset($url_info['port']) ? $url_info['port'] : 80;
        $path = isset($url_info['path']) ? $url_info['path'] : '/';

        // 防止空格转化为+号
        $params = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        if ($method === self::METHOD_GET && strlen($params) > 0) {
            $path .= '?' . $params;
        }

        $fp = fsockopen($host, $port, $error_code, $error_info, 1);
        try {
            if ($fp === false) {
                throw new \Exception('fsockopen error code: ' . $error_code . ', error info: ' . $error_info);
            } else {
                $http = "$method $path HTTP/1.1\r\n";
                $http .= "Host: $host\r\n";
                $connection_close = "Connection:close\r\n\r\n";
                if ($method === self::METHOD_POST) {
                    // 拼接 post 数据
                    $http .= "Content-type: application/x-www-form-urlencoded\r\n";
                    $http .= "Content-Length: " . strlen($params) . "\r\n";
                    $http .= $connection_close;
                    $http .= $params;
                } else {
                    $http .= $connection_close;
                }
                fwrite($fp, $http);
                fclose($fp);
            }
        } catch (\Exception $e) {
            // todo log
        }
    }
}