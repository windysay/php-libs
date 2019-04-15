<?php

namespace JMD\Libs\Services;

use JMD\App\Utils;
use JMD\Cache\Cookie\Sso\TicketCookie;
use JMD\Utils\SsoHelper;

/**
 *
 * Class SsoService
 * @package JMD\Libs\Services
 */
class SaasService
{

    public static function request($route, $postData, $endpoint = '')
    {
        $request = new BaseRequest();
        $request->setUrl($route);
        if($endpoint != ''){
            $request->setEndpoint($endpoint);
        }
        $request->setData($postData);
        return $request->execute();
    }

}
