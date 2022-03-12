<?php

/**
 * PHP SDK for Cpay OpenAPI--only for APPs which are hosting on CEE_V2.
 *
 * @version   3.0.6
 * @author    open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:
 *          3.0.6 | looklu| 2012-12-05 17:22:51 | Support POST mode to send the request.
 */

namespace Tencent\QQ\Open;

use GuzzleHttp\Exception\GuzzleException;
use Tencent\QQ\Exceptions\QQAPIRouteException;
use Tencent\QQ\Exceptions\QQOpenIDException;
use Tencent\QQ\Exceptions\QQResponseException;

/**
 * 支付 SDK，仅适用于部署在 CEE_V2 上的应用，依赖于 PHP_SDK v3.0.2 及以上版本。
 *
 * @version 3.0.0
 */
class CloudCeePay extends OpenAPIv3 {

    /**
     * 执行支付 API 调用，返回结果数组
     *
     * @url https://wikinew.open.qq.com/index.html#/iwiki/940617555
     *
     * @param array  $params 调用支付 API 时带的参数
     * @param string $method
     * @param array  $headers
     *
     * @return array
     * @throws GuzzleException
     * @throws QQAPIRouteException
     * @throws QQOpenIDException
     * @throws QQResponseException
     */
    public function buyGoods(array $params, string $method = 'POST', array $headers = []): array {
        $cee_extend = getenv('CEE_DOMAINNAME') . '*'
            . getenv('CEE_VERSIONID') . '*'
            . getenv('CEE_WSNAME');
        $params['cee_extend'] = $cee_extend;

        return $this->api('/v3/pay/buy_goods', $method, $params, $headers);
    }
}
