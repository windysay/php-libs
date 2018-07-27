<?php

namespace JMD\Libs\Services;

class DataFormat
{
    protected $srcData;

    const OUTPUT_SUCCESS = 18000;

    const OUTPUT_ERROR = 13000;

    public function __construct($srcData)
    {
        if (is_string($srcData)) {
            $srcData = json_decode($srcData, true);
        }
        $this->srcData = $srcData;
    }

    public function isSuccess()
    {
        return $this->srcData['code'] == self::OUTPUT_SUCCESS;
    }

    public function isError()
    {
        return $this->srcData['code'] == self::OUTPUT_ERROR;
    }

    public function getMsg()
    {
        return $this->srcData['msg'];
    }

    public function getData()
    {
        return $this->srcData['data'];
    }

    public function getDataField($field)
    {
        $data = $this->srcData['data'];

        if (!is_array($data)) {
            return '';
        }

        if (!array_key_exists($field, $data)) {
            return '';
        }

        return $data[$field];

    }


}