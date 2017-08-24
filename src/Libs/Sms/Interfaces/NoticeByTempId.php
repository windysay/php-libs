<?php
namespace JMD\Libs\Sms\Interfaces;

interface NoticeByTempId
{
    public function sendNoticeByTempId($mobile, $tempId, $tempPara = []);

}
