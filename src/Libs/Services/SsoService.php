<?php

namespace JMD\Libs\Services;

/**
 *
 * Class SsoService
 * @package JMD\Libs\Services
 */
class SsoService
{
    /**
     * 鉴权
     *
     * @param $mobile
     * @param $code
     * @param $app_name
     * @return DataFormat
     * @throws \Exception
     */
    public static function httpSsoCheck($ticket, $ip, $actionId)
    {
        $request = new BaseRequest();
        $url = 'oa/api/sso/token';
        $request->setUrl($url);

        $host = $_SERVER['HTTP_HOST'];
        $post_data = [
            'ticket' => $ticket,
            'ip' => $ip,
            'Referer' => $host,
            'route' => $actionId,
        ];

        $request->setData($post_data);
        return $request->execute();
    }

    /**
     * 用户信息获取
     *
     * @param null $page
     * @param null $keyword
     * @param null $id
     * @param null $pageSize
     *
     * @return DataFormat
     */
    public static function httpUserInfo($page = null, $keyword = null, $id = null, $pageSize = null)
    {
        $request = new BaseRequest();
        $url = 'oa/api/user/info';
        $request->setUrl($url);

        $post_data = [];

        if ($page !== null) {
            $post_data['page'] = $page;
        }
        if ($keyword !== null) {
            $post_data['keyword'] = $keyword;
        }
        if ($pageSize !== null) {
            $post_data['pageSize'] = $pageSize;
        }
        if ($id !== null) {
            $post_data['id'] = $id;
        }

        $request->setData($post_data);
        return $request->execute();
    }

}