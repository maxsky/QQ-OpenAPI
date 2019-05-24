<?php

/**
 * 发送HTTP网络请求类
 *
 * @version 3.0.0
 * @author open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:
 *           3.0.1 | coolinchen | 2012-09-07 10:30:00 | add funtion makeRequestWithFile
 *           3.0.0 | nemozhang | 2011-03-09 15:33:04 | initialization
 */

namespace Tencent\QQ\Open\lib;

class SnsNetwork {

    /**
     * 执行一个 HTTP 请求
     *
     * @param string       $url 执行请求的 URL
     * @param array|string $params 表单参数。可以是 array, 也可以是经过 url 编码之后的 string
     * @param array|string $cookie 可以是 array, 也可以是经过拼接的 string
     * @param string       $method 请求方式，默认 post
     *
     * @return array
     */
    public static function makeRequest($url, $params, $cookie = '', $method = 'post') {
        $query_string = self::makeQueryString($params);

        $cookie_string = self::makeCookieString($cookie);

        $ch = curl_init();

        if (strtoupper($method) === 'POST') {
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $query_string,
                CURLOPT_HTTPHEADER => ['Expect:'] // disable 100-continue
            ];
        } else {
            $options = [
                CURLOPT_URL => "{$url}?{$query_string}"
            ];
        }

        $options += [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0
        ];

        if ($cookie_string) {
            $options[CURLOPT_COOKIE] = $cookie_string;
        }

        curl_setopt_array($ch, $options);

        $ret = curl_exec($ch);
        $err = curl_error($ch);

        if ($ret === false || !empty($err)) {
            $errno = curl_errno($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return [
                'result' => false,
                'errno' => $errno,
                'msg' => $err,
                'info' => $info,
            ];
        }

        curl_close($ch);

        return [
            'result' => true,
            'msg' => $ret,
        ];
    }

    /**
     * 执行一个 HTTP POST 请求，multipart/form-data 类型上传文件
     *
     * @param string       $url 执行请求的 URL
     * @param array        $params 表单参数，对于文件表单项直接传递文件的全路径, 并在前面增加'@'符号。
     *                             举例: ['upload_file' = >'@/home/xxx/hello.jpg', 'field1' => 'value1'];
     * @param array|string $cookie 可以是 array, 也可以是经过拼接的 string
     *
     * @return array 结果数组
     */
    public static function makeRequestWithFile($url, $params, $cookie = '') {
        $cookie_string = self::makeCookieString($cookie);

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['Expect:'], // disable 100-continue
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0
        ];

        if ($cookie_string) {
            $options[CURLOPT_COOKIE] = $cookie_string;
        }

        curl_setopt_array($ch, $options);

        $ret = curl_exec($ch);
        $err = curl_error($ch);

        if ($ret === false || !empty($err)) {
            $errno = curl_errno($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return [
                'result' => false,
                'errno' => $errno,
                'msg' => $err,
                'info' => $info,
            ];
        }

        curl_close($ch);

        return [
            'result' => true,
            'msg' => $ret,
        ];
    }

    public static function makeQueryString($params) {
        if (is_string($params))
            return $params;

        $query_string = [];
        foreach ($params as $key => $value) {
            array_push($query_string, rawurlencode($key) . '=' . rawurlencode($value));
        }
        $query_string = implode('&', $query_string);
        return $query_string;
    }

    /**
     * @param $params
     *
     * @return string
     */
    private static function makeCookieString($params) {
        if (!$params) {
            return '';
        }
        if (is_string($params)) {
            return trim($params);
        }
        $cookie_string = [];
        foreach ($params as $key => $value) {
            array_push($cookie_string, $key . '=' . $value);
        }
        $cookie_string = implode('; ', $cookie_string);

        return trim($cookie_string);
    }
}