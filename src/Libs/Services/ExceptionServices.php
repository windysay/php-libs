<?php

namespace JMD\Libs\Services;

class ExceptionServices
{
    /**
     * 异常抛送
     * @param $exception
     * @param null $requestUri
     * @param string $requsetMethod
     * @param null $remoteAddr
     * @return mixed
     * @throws \Exception
     */
    public static function send(
        $exception,
        $requestUri = null,
        $requsetMethod = 'GET',
        $remoteAddr = null
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