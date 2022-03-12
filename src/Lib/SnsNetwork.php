<?php

/**
 * 发送HTTP网络请求类
 *
 * @version   3.0.0
 * @author    open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:
 *           3.0.1 | coolinchen | 2012-09-07 10:30:00 | add funtion makeRequestWithFile
 *           3.0.0 | nemozhang | 2011-03-09 15:33:04 | initialization
 */

namespace Tencent\QQ\Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SnsNetwork {

    /**
     * 执行一个 HTTP 请求
     *
     * @param string $url     执行请求的 URL
     * @param string $method  请求方式，默认 post
     * @param array  $params  表单参数
     * @param array  $headers 请求头
     *
     * @return array
     * @throws GuzzleException
     */
    public static function makeRequest(string $url, string $method, array $params, array $headers): array {
        if (strtoupper($method) === 'GET') {
            $options = [
                'headers' => $headers,
                'query' => $params
            ];
        } else {
            $options = [
                'headers' => $headers,
                'form_params' => $params
            ];
        }

        $httpClient = new Client();

        $response = $httpClient->request($method, $url, $options)->getBody();

        return json_decode($response, true);
    }

    /**
     * 执行一个 HTTP POST 请求，multipart/form-data 类型上传文件
     *
     * @param string $url          执行请求的 URL
     * @param array  $params       表单参数，对于文件表单项直接传递文件的全路径, 并在前面增加'@'符号。
     *                             举例: ['upload_file' = >'@/home/xxx/hello.jpg', 'field1' => 'value1'];
     * @param array  $headers
     *
     * @return array 结果数组
     * @throws GuzzleException
     */
    public static function makeRequestWithFile(string $url, array $params, array $headers = []): array {
        $httpClient = new Client();

        $response = $httpClient->post($url, [
            'headers' => $headers,
            'multipart' => $params
        ])->getBody();

        return json_decode($response, true);
    }
}
