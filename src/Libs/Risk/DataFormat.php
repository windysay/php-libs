<?php

namespace JMD\Libs\Risk;

class DataFormat
{
    protected $srcData;

    const OUTPUT_SUCCESS = 18000;

    const OUTPUT_ERROR = 12000;

    public function __construct($srcData)
    {
        if (is_string($srcData)) {
            $srcData = json_decode($srcData, true);
        }
        $this->srcData = $srcData;
    }

    public function isSuccess()
    {
        return $this->getCode() == self::OUTPUT_SUCCESS;
    }

    public function isError()
    {
        return $this->getCode() == self::OUTPUT_ERROR;
    }

    public function getMsg()
    {
        return $this->srcData['msg'] ?? null;
    }

    public function getData()
    {
        return $this->srcData['data'] ?? null;
    }

    public function getCode()
    {
        return $this->srcData['code'] ?? null;
    }

    public function getDataField($field)
    {
        $data = $this->getData();
        if (!is_array($data)) {
            return '';
        }
        if (!array_key_exists($field, $data)) {
            return '';
        }
        return $data[$field];
    }
}
