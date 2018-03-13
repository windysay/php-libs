<?php
namespace JMD\Libs\Sms\Interfaces;

interface VoiceSmsBase
{
    public function sendVoiceCaptcha();

    public function sendVoiceNotice($tmpId);
}
