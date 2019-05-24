<?php

/**
 * PHP SDK for Cpay OpenAPI--only for APPs which are hosting on CEE_V2.
 *
 * @version 3.0.6
 * @author open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:
 *          3.0.6 | looklu| 2012-12-05 17:22:51 | Support POST mode to send the request.
 */

namespace Tencent\QQ\Open;

/**
 * 支付 SDK，仅适用于部署在 CEE_V2 上的应用，依赖于 PHP_SDK_V3.0.2 及以上版本。
 *
 * @version 3.0.0
 */
class CloudCeePay extends OpenAPIV3 {

    /**
     * 构造函数
     *
     * @param string $server_ip 测试环境为：1.254.254.22；正式环境为：openapi.tencentyun.com
     * @param int    $appId 应用 ID
     * @param string $appKey 应用密钥
     */
    public function __construct($server_ip, $appId, $appKey) {
        parent::__construct($appId, $appKey);
        parent::setServerName($server_ip);
    }

    /**
     * 执行支付 API 调用，返回结果数组
     *
     * @param array        $params 调用支付 API 时带的参数 参考 http://wiki.open.qq.com/wiki/v3/pay/buy_goods
     * @param array|string $cookie
     *
     * @return array 结果数组
     */
    public function buyGoods($params, $cookie = '') {
        $cee_extend = getenv('CEE_DOMAINNAME') . '*'
            . getenv('CEE_VERSIONID') . '*'
            . getenv('CEE_WSNAME');
        $params['cee_extend'] = $cee_extend;
        $ret = parent::api('/v3/pay/buy_goods', $params, $cookie);
        return $ret;
    }
}