<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 9:30
 */

namespace JMD\Libs\Services;


class EmailServers
{
    public static $url = 'http://wserver/api/queue/';

    public static function publish($data)
    {
        $request = new BaseRequest();
        $url = 'queue/push';
        $request->setUrl($url);
        $request->setData($data);
        $request->domain = self::$url;
        return json_decode($request->execute(), true);
    }

    public static function getQueue()
    {
        $request = new BaseRequest();
        $url = 'queue/get-queue';
        $request->setUrl($url);
        $request->domain = self::$url;
        return json_decode($request->execute(false), true);
    }

    public static function sendEmail($data)
    {
        $request = new BaseRequest();
        $url = 'queue/send';
        $sendData = [
            'to' => $data['to'],
            'form' => $data['from'],
            'title' => $data['title'],
            'message' => $data['message'],
            'queue' => $data['queue'],
        ];
        $request->setUrl($url);
        $request->setData($sendData);
        return json_decode($request->execute(), true);
    }
}