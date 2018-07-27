<?php

namespace JMD\Libs\Services;

class ExceptionServices
{
    /** @var BaseRequest */
    private $request;

    /**
     * Exception constructor.
     *
     * @param \Exception $exception       异常内容
     * @param null       $requestUri      请求uri
     * @param string       $requsetMethod 请求方式
     * @param string       $remoteAddr    请求IP
     */
    public function __construct(
        $exception,
        $requestUri = null,
        $requsetMethod = 'GET',
        $remoteAddr = null
    ) {
        $this->request = new BaseRequest();
        $this->request->setUrl('api/exception');
        $this->addBaseRequest('data', [
            'exception' => $exception,
            'request_uri' => $requestUri,
            'request_method' => $requsetMethod,
            'remote_addr' => $remoteAddr
        ]);
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