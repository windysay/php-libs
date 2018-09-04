<?php

namespace JMD\Libs\Services;

class ExceptionServices
{
    /**
     * 异常抛送
     * @param string|array $exception
     * @param string|array $receiver
     * @return DataFormat
     * @throws \Exception
     */
    public static function send(
        $exception,
        $receiver = 'develop-alert@jiumiaodai.com'
    ) {
        $request = new BaseRequest();
        $url = 'api/exception';
        $request->setUrl($url);
        $request->setUrl('api/exception');
        $params = [
            'exception' => $exception,
            'receiver' => $receiver
        ];
        $request->setData($params);
        return $request->execute();
    }
}