<?php

namespace JMD\Libs\Services;

use JMD\App\Utils;

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

        //鉴权使用sso外网地址
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        if(isset($config['sso_endpoint'])){
            $request->setEndpoint($config['sso_endpoint']);
        }
        $request->setData($post_data);
        return $request->execute();
    }

    /**
     * 用户信息获取 通过 分页 关键字
     *
     * @param null $page
     * @param null $keyword
     * @param null $pageSize
     * @return DataFormat
     */
    public static function getUserList($page = null, $keyword = null, $pageSize = null)
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

        $request->setData($post_data);
        return $request->execute();
    }

    /**
     * 用户信息获取 通过id id可传数组
     *
     * @param $id
     * @return DataFormat
     */
    public static function getUserById($id)
    {
        $request = new BaseRequest();
        $url = 'oa/api/user/info';
        $request->setUrl($url);

        $post_data = [];

        if ($id !== null) {
            $post_data['id'] = $id;
        }

        $request->setData($post_data);
        return $request->execute();
    }

}