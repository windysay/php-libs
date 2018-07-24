<?php

namespace JMD\Libs\Services;

use JMD\Libs\Risk\DataFormat;

class ExceptionServices
{
    /** @var BaseRequest */
    private $request;

    /**
     * Exception constructor.
     * @param $appKey
     * @param $secretKey
     * @param $exception | 异常内容
     * @param null $requestUri | 请求uri
     * @param null $httpHost | 请求方式
     * @param null $requsetMethod | 请求方式
     * @param null $remoteAddr | 请求IP
     */
    public function __construct(
        $data
    ) {
        $this->request = new BaseRequest();
        $this->request->setUrl('api/exception');
        $this->addBaseRequest('data', $data);
    }

    /**
     * @param $field
     * @param $val
     */
    private function addBaseRequest($field, $val)
    {
        $data = $this->request->data;
        $data[$field] = $val;
        $this->request->setData($data);
    }

    /**
     * @return DataFormat
     * @throws \Exception
     */
    public function execute()
    {
        return new DataFormat($this->request->execute());
    }

}