<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2022/3/10
 * Time: 7:36 PM
 */

namespace Tencent\QQ\Utils;

use GuzzleHttp\Psr7\Uri;

class PrintUtil {

    /**
     * 打印出请求串的内容，当 API 中的这个函数的注释放开将会被调用。
     *
     * @param string $url    请求串内容
     * @param string $method 请求方法
     * @param array  $params 请求参数
     */
    public static function printRequest(string $url, string $method, array $params) {
        $uri = (new Uri($url))->withQuery(http_build_query($params));

        $queryStr = $uri->getQuery();

        if ($method == 'get') {
            $url = "$url?$queryStr";
        }

        echo "\n================ Request Info ================\n\n";

        print_r("Method      : $method\n");
        print_r("URL         : $url\n");

        if ($method == 'post') {
            print_r("Query String: $queryStr\n");
        }

        echo "\n";
        print_r('params      : ' . print_r($params, true) . "\n");
        echo "\n";
    }

    /**
     * 打印出返回结果的内容，当 API 中的这个函数的注释放开将会被调用。
     *
     * @param array $array 待打印的 array
     */
    public static function printResponse(array $array) {
        echo "\n================ Response Info ================\n\n";
        print_r($array);
        echo "\n";
    }
}
