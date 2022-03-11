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

class UserAPI extends OpenAPIv3 {

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
