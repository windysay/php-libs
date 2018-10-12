<?php

namespace JMD\Libs\Sso;


use JMD\App\Utils;
use JMD\Libs\Services\SsoService;

class SsoUser
{
    const TICKET_LOCAL_CACHE = 60 * 30; //设置默认本地ticket缓存时间为30分钟

    /**
     * sso鉴权获取用户信息
     * @param $actionId 路由动作
     * @param null $ticket_local_cache ticket本地缓存时间
     * @return mixed
     */
    public static function get_user_info($actionId, $ticket_local_cache = null)
    {
//        $http_type = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $load_domain_config = DomainConfig::base_domain();
        if (empty($_COOKIE[$load_domain_config['ticket_cookie_name']])) {
            $redirect_url = $http_type . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url = $load_domain_config['login_redirect_url'] . '?redirect_uri=' . $redirect_url;
            header("Location: " . $url);
            exit();
        }
        $ticket = $_COOKIE[$load_domain_config['ticket_cookie_name']];
        $ip = Basic::getIp();
        $redis = Utils::redis();
        if (empty($redis->exists('sso_staff:flag:' . md5($ticket))) || $actionId == 'index/logout') {
            //执行时间
            $start = microtime(true) * 1000;

            //$body = self::http_sso_check($ticket, $ip, $actionId);
            $result = SsoService::httpSsoCheck($ticket, $ip, $actionId);
            if ($result && $result->isSuccess()) {
                $body = json_encode($result->getData(), 256);
            } else {
                Utils::alert('【九秒贷】单点登录鉴权异常',
                    json_encode([
                        'ticket' => $ticket,
                        'ip' => $ip,
                        'actionId' => $actionId,
                        'msg' => $result->getMsg(),
                        'data' => $result->getData(),
                    ], 256), ['chengxusheng@jiumiaodai.com']);
                return $result->getData();
            }

            $top = microtime(true) * 1000;
            //存入redis list
            $diff_ms = $top - $start;
            $json = '{"date":"' . date('Y-m-d H:i:s') . '","url":"' . $http_type . '' . $_SERVER['HTTP_HOST'] . '/' . $actionId . '","ms":"' . $diff_ms . '"}';
            $redis->rpush('sso_curl', $json);
            //监控url 时间设置
            if ($redis->ttl('sso_curl') == -1) {
                $redis->expire('sso_curl', 3600 * 6);
            }
            $response_data = json_decode($body, true);
            if ($response_data['code'] == '1001') {
                return $response_data;
            }
            //默认设置缓存 30分钟
            $redis->set('sso_staff:flag:' . md5($ticket), $body);
            $ticket_local_cache = is_null($ticket_local_cache) ? self::TICKET_LOCAL_CACHE : $ticket_local_cache;
            $redis->expire('sso_staff:flag:' . md5($ticket), $ticket_local_cache);
        }
        $user_data = json_decode($redis->get('sso_staff:flag:' . md5($ticket)), true);
        return $user_data;
    }

    /**
     * sso请求鉴权
     * @param $ticket
     * @param $ip
     * @param $actionId
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function http_sso_check($ticket, $ip, $actionId)
    {
        $host = $_SERVER['HTTP_HOST'];
        $load_domain_config = DomainConfig::base_domain();
        $data = [
            'headers' => [
                'User-Agent' => 'testing/1.0',
                'Accept' => 'application/json',
                'ticket' => $ticket,
                'ip' => $ip,
                'Referer' => $host,
                'route' => $actionId,
            ],
        ];
        return Basic::httpClient($load_domain_config['http_sso_check'], 'get', $data);
    }
}