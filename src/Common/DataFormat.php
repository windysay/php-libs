<?php

namespace JMD\Common;

class DataFormat
{
    protected $srcData;

    const OUTPUT_SUCCESS = 18000;

    const OUTPUT_ERROR = 13000;

    /**
     * DataFormat constructor.
     * @param $srcData
     */
    public function __construct($srcData)
    {
        if (is_string($srcData)) {
            $srcData = json_decode($srcData, true);
        }
        $this->srcData = $srcData;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getCode() == self::OUTPUT_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->getCode() == self::OUTPUT_ERROR;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->srcData['msg'] ?? 'php-libs访问异常';
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->srcData['data'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->srcData['code'] ?? self::OUTPUT_ERROR;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->srcData;
    }

    /**
     * @param $field
     * @return mixed|string
     */
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