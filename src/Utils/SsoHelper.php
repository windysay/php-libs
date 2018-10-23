<?php

namespace JMD\Utils;

use JMD\App\Utils;
use JMD\Cache\Cookie\Sso\TicketCookie;

class SsoHelper
{
    /**
     * @param $ticket
     * @return mixed
     */
    public static function getTicket($ticket)
    {
        //如果有get的ticket，获取并写入cookie
        if ($ticket) {
            if(strstr('+', $ticket)) {
                $ticket = str_replace('+', '%2B', urlencode($ticket));
            }
            TicketCookie::set($ticket);
            return $ticket;
        }
        //否则读cookie的ticket
        $ticket = TicketCookie::get();
        return $ticket;
    }
}