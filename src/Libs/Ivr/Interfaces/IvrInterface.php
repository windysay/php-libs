<?php
/**
 * Created by PhpStorm.
 * User: Summer
 * Date: 2018/4/4
 * Time: 16:01
 */

namespace JMD\Libs\Ivr\Interfaces;

interface IvrInterface
{
    public function send($phone, $orderId);

    public static function getInstance();
}