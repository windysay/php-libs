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

    const TICKET_LOCAL_CACHE = 60 * 30; //设置默认本地ticket缓存时间为30分钟
    const TICKET_COOKIE_NAME = 'ticket_prod';//默认cookie的ticket键名

    /**
     * sso鉴权获取用户信息 ticket为前端get的ticket值，可传空
     *
     * @param $ticket
     * @param $ip
     * @param $actionId
     * @param null $ticket_local_cache
     * @return array|mixed
     */
    public static function getUserInfo($ticket, $ip, $actionId, $ticket_local_cache = null)
    {
        //为前端写入登录退出cookie
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        @setcookie('redirect_uri', $config['sso_endpoint'] . 'sso/login', time() + 43200, '/', Utils::getHost());
        @setcookie('logout_url', $config['sso_endpoint'] . 'sso/logout', time() + 43200, '/', Utils::getHost());
        //cookie和get同时没有ticket，返回1001
        if (empty($_COOKIE[self::TICKET_COOKIE_NAME]) && !$ticket) {
            return ['code' => '1001'];
        }
        //如果有get的ticket，获取并写入cookie，否则读cookie的ticket
        if($ticket){
            $ticket = str_replace('+', '%2B', urlencode($ticket));
            @setcookie(self::TICKET_COOKIE_NAME, $ticket, time() + 43200, '/', Utils::getHost());
        }else{
            $ticket = $_COOKIE[self::TICKET_COOKIE_NAME];
        }
        //获取redis缓存，存在则直接返回缓存数据
        $redis = Utils::redis();
        if(!empty($redis->exists('sso_staff:flag:' . md5($ticket))) && $redis->get('sso_staff:flag:' . md5($ticket)) != 'null' && $actionId != 'index/logout'){
            $user_data = json_decode($redis->get('sso_staff:flag:' . md5($ticket)), true);
            return $user_data;
        }
        //redis不存在则进行鉴权
        $result = SsoService::httpSsoCheck($ticket, $ip, $actionId);
        if(!$result){
            Utils::alert('单点登录鉴权异常',
                json_encode([
                    'ticket' => $ticket,
                    'ip' => $ip,
                    'actionId' => $actionId,
                ], 256), ['chengxusheng@jiumiaodai.com']);
            return ['code' => '1001'];
        }
        $body = json_encode($result->getData(), 256);
        $response_data = json_decode($body, true);
        //鉴权失败，清除cookie和redis中的ticket
        if ($response_data['code'] == '1001') {
            @setcookie(self::TICKET_COOKIE_NAME,null, null, '/', Utils::getHost());
            $redis->del('sso_staff:flag:' . md5($ticket));
            return $response_data;
        }
        //默认设置缓存 30分钟
        $redis->set('sso_staff:flag:' . md5($ticket), $body);
        $ticket_local_cache = is_null($ticket_local_cache) ? self::TICKET_LOCAL_CACHE : $ticket_local_cache;
        $redis->expire('sso_staff:flag:' . md5($ticket), $ticket_local_cache);
        return $response_data;
    }

    /**
     * 鉴权
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
        if (isset($config['sso_endpoint'])) {
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
     * @param string|array $ids
     * @return DataFormat
     * @throws \Exception
     */
    public static function getUserByIds($ids)
    {
        $request = new BaseRequest();
        $url = 'oa/api/user/info';
        $request->setUrl($url);

        $post_data = [];

        if ($ids !== null) {
            $post_data['id'] = $ids;
        }

        $request->setData($post_data);
        return $request->execute();
    }

    /**------------九秒贷专用Start------------*/
    /**
     * 昵称接口
     *
     * @param $id
     * @return DataFormat
     */
    public static function httpUserAgent($id)
    {
        $request = new BaseRequest();
        $url = 'oa/api/user/agent';
        $request->setUrl($url);

        $post_data = [
            'id' => $id,
        ];

        $request->setData($post_data);
        return $request->execute();
    }

    /**
     * 渠道用户添加接口
     *
     * @param $channel_account
     * @param $channel_name
     * @return DataFormat
     */
    public static function httpChannelAdd($channel_account, $channel_name)
    {
        $request = new BaseRequest();
        $url = 'oa/api/channel/add';
        $request->setUrl($url);

        $post_data = [
            'username' => $channel_account,
            'nickname' => $channel_name,
        ];

        $request->setData($post_data);
        return $request->execute();
    }
    /**------------九秒贷专用End------------*/

}