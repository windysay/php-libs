<?php

namespace JMD\Libs\Services;

class ExceptionServices
{
    /**
     * 异常抛送
     * @param $exception
     * @param string $requestUri
     * @param string $requsetMethod
     * @param string $remoteAddr
     * @return DataFormat
     * @throws \Exception
     */
    public static function send(
        $exception,
        $requestUri = '/',
        $requsetMethod = 'GET',
        $remoteAddr = '127.0.0.1'
    ) {
        $request = new BaseRequest();
        $url = 'api/exception';
        $request->setUrl($url);
        $request->setUrl('api/exception');
        $params['data'] = [
            'exception' => $exception,
            'request_uri' => $requestUri,
            'request_method' => $requsetMethod,
            'remote_addr' => $remoteAddr
        ];
        $request->setData($params);
        return $request->execute();
    }
}