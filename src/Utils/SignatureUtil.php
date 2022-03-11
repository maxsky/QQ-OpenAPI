<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2022/3/10
 * Time: 7:17 PM
 */

namespace Tencent\QQ\Utils;

class SignatureUtil {

    /**
     * @param string $route
     * @param string $method
     * @param array  $params
     * @param string $app_key
     *
     * @return string
     */
    public static function makeSig(string $route, string $method, array $params, string $app_key): string {
        $str = strtoupper($method) . '&' . rawurlencode($route) . '&';

        unset($params['sig']);

        ksort($params);

        $query_string = [];

        foreach ($params as $key => $val) {
            $query_string[] = $key . '=' . $val;
        }

        $query_string = implode('&', $query_string);

        $source = $str . str_replace('~', '%7E', rawurlencode($query_string));

        $signed = hash_hmac('sha1', $source, strtr($app_key, '-_', '+/'), true);

        return base64_encode($signed);
    }
}
