<?php

namespace JMD\Libs\Sso\Interfaces;

interface SsoIdentity
{
    public static function httpClient($url, $method, array $data = null);

    public static function curl_admin_data();

    public static function curl_admin_add($data);

    public static function curl_admin_edit($data);

    public static function get_id_info($id);

    public static function get_company_user($company_id);

}