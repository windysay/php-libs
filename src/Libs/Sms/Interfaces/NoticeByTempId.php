<?php
namespace JMD\Libs\Sms\Interfaces;

interface NoticeByTempId
{
    public function sendNotice($mobile, $tempId, $tempPara = []);

}
