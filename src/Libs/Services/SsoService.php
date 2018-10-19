<?php

namespace JMD\Libs\Services;

use JMD\App\Utils;
use JMD\Cache\Cookie\Sso\TicketCookie;

/**
 *
 * Class SsoService
 * @package JMD\Libs\Services
 */
class SsoService
{

    /**
     * @var array|mixed
     */
    private $userData;

    const LOGIN_STATUS_SUCCESS = 200;//已登录，成功登录
    const LOGIN_STATUS_FAIL = 1001;//未登录，已退出，过期

    const DEFAULT_REDIRECT_URI = 'https://sso.jiumiaodai.com/';//未设置sso_endpoint默认使用九秒贷sso

    const TICKET_LOCAL_CACHE = 60 * 30; //设置默认本地ticket缓存时间为30分钟
    const TICKET_COOKIE_NAME = 'ticket_prod';//默认cookie的ticket键名

    /**
     * SsoService constructor.
     * @param $ticket
     * @param $ip
     * @param $actionId
     * @param null $ticketLocalCache
     */
    public function __construct($ticket, $ip, $actionId, $ticketLocalCache = null)
    {
        $this->userData = self::getUserInfo($ticket, $ip, $actionId, $ticketLocalCache);
    }

    /**
     * 判断是否登录
     *
     * @return bool
     */
    public function isLogin()
    {
        if (isset($this->userData['code']) && $this->userData['code'] == self::LOGIN_STATUS_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * 返回用户数据
     *
     * @return array|mixed
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * 获取回跳地址
     *
     * @return string
     */
    public function getLoginUri()
    {
        $sso_login_url = $this->userData['sso_login_url'] ?? 'https://sso.jiumiaodai.com/sso/login';
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $redirect_url = $http_type . $_SERVER['HTTP_HOST'];
        $url = $sso_login_url . '?redirect_uri=' . $redirect_url;
        return $url;
    }

    /**
     * sso鉴权获取用户信息 ticket为前端get的ticket值，可传空
     *
     * @param $ticket
     * @param $ip
     * @param $actionId
     * @param null $ticketLocalCache
     * @return array|mixed
     */
    public static function getUserInfo($ticket, $ip, $actionId, $ticketLocalCache = null)
    {
        //为前端写入登录退出cookie
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        $redirectUri = $config['sso_endpoint'] . 'sso/login';
        $logoutUrl = $config['sso_endpoint'] . 'sso/logout';
        @setcookie('redirect_uri', $redirectUri, time() + 43200, '/', Utils::getHost());
        @setcookie('logout_url', $logoutUrl, time() + 43200, '/', Utils::getHost());
        //cookie和get同时没有ticket，返回1001
        //if (empty($_COOKIE[self::TICKET_COOKIE_NAME]) && !$ticket) {
        if (!TicketCookie::get() && !$ticket) {
            return [
                'code' => self::LOGIN_STATUS_FAIL,
                'sso_login_url' => $redirectUri,
                'sso_logout_url' => $logoutUrl,
                'msg' => 'no ticket',
            ];
        }
        //如果有get的ticket，获取并写入cookie，否则读cookie的ticket
        if ($ticket) {
            $ticket = str_replace('+', '%2B', urlencode($ticket));
            //@setcookie(self::TICKET_COOKIE_NAME, $ticket, time() + 43200, '/', Utils::getHost());
            TicketCookie::set($ticket);
        } else {
            $ticket = $_COOKIE[self::TICKET_COOKIE_NAME];
        }
        //获取redis缓存，存在则直接返回缓存数据
        $redis = Utils::redis();
        if (!empty($redis->exists('sso_staff:flag:' . md5($ticket))) && $redis->get('sso_staff:flag:' . md5($ticket)) != 'null' && $actionId != 'index/logout') {
            $user_data = json_decode($redis->get('sso_staff:flag:' . md5($ticket)), true);
            return $user_data;
        }
        //redis不存在则进行鉴权
        $result = SsoService::httpSsoCheck($ticket, $ip, $actionId);
        if (!$result) {
            Utils::alert('单点登录鉴权异常',
                json_encode([
                    'ticket' => $ticket,
                    'ip' => $ip,
                    'actionId' => $actionId,
                ], 256), ['chengxusheng@jiumiaodai.com']);
            return [
                'code' => self::LOGIN_STATUS_FAIL,
                'sso_login_url' => $redirectUri,
                'sso_logout_url' => $logoutUrl,
                'msg' => 'ticket fail',
            ];
        }
        $body = json_encode($result->getData(), 256);
        $response_data = json_decode($body, true);
        //鉴权失败，清除cookie和redis中的ticket
        if ($response_data['code'] == self::LOGIN_STATUS_FAIL) {
            //@setcookie(self::TICKET_COOKIE_NAME, null, null, '/', Utils::getHost());
            TicketCookie::del();
            $redis->del('sso_staff:flag:' . md5($ticket));
            return $response_data;
        }
        //默认设置缓存 30分钟
        $redis->set('sso_staff:flag:' . md5($ticket), $body);
        $ticketLocalCache = is_null($ticketLocalCache) ? self::TICKET_LOCAL_CACHE : $ticketLocalCache;
        $redis->expire('sso_staff:flag:' . md5($ticket), $ticketLocalCache);
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

    /**
     * 用户列表获取(子微服务拉父微服务用户数据)
     *
     * @param null $page
     * @param null $keyword
     * @param null $pageSize
     * @return DataFormat
     */
    public static function getSsoUserList()
    {
        $request = new BaseRequest();
        $url = 'oa/api/user/info';
        $request->setUrl($url);

        $post_data = [];

        //访问父sso外网地址
        $config = Utils::getParam(BaseRequest::CONFIG_NAME);
        if (isset($config['sso_endpoint'])) {
            $request->setEndpoint($config['sso_endpoint']);
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
     * @throws \Exception
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
     * @param $channelAccount
     * @param $channelName
     * @return DataFormat
     * @throws \Exception
     */
    public static function httpChannelAdd($channelAccount, $channelName)
    {
        $request = new BaseRequest();
        $url = 'oa/api/channel/add';
        $request->setUrl($url);

        $post_data = [
            'username' => $channelAccount,
            'nickname' => $channelName,
        ];

        $request->setData($post_data);
        return $request->execute();
    }
    /**------------九秒贷专用End------------*/

}