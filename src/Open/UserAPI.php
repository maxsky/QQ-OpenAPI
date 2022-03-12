<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2022/3/11
 * Time: 12:51 PM
 */

namespace Tencent\QQ\Open;

use GuzzleHttp\Exception\GuzzleException;
use Tencent\QQ\Exceptions\QQAPIRouteException;
use Tencent\QQ\Exceptions\QQOpenIDException;
use Tencent\QQ\Exceptions\QQResponseException;
use Tencent\QQ\Lib\SnsNetwork;

class UserAPI extends OpenAPIv3 {

    /**
     * @param string $access_token
     * @param int    $union_id
     * @param string $fmt
     *
     * @return array
     * @throws GuzzleException
     */
    public function getUnionId(string $access_token, int $union_id = 1, string $fmt = 'json'): array {
        return SnsNetwork::makeRequest('https://graph.qq.com/oauth2.0/me', 'GET', [
            'access_token' => $access_token,
            'unionid' => $union_id,
            'fmt' => $fmt
        ]);
    }

    /**
     * @param string $access_token
     * @param string $open_id
     *
     * @return array
     * @throws GuzzleException
     * @throws QQAPIRouteException
     * @throws QQOpenIDException
     * @throws QQResponseException
     */
    public function getInfo(string $access_token, string $open_id): array {
        return $this->api('/v3/user/get_info', 'POST', [
            'openkey' => $access_token,
            'openid' => $open_id,
            'pf' => 'qzone'
        ]);
    }
}
