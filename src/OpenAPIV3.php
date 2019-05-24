<?php

/**
 * PHP SDK for OpenAPI V3
 *
 * @version 3.0.9
 * @author open.qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 * @ History:
 *           3.0.9 | coolinchen| 2013-05-30 11:14:12 | remove that SDK check whether the openkey is empty
 *           3.0.8 | coolinchen| 2013-05-03 15:30:12 | resolve a bug: the signature verification is not passed
 *                                                     when the method is GET
 *           3.0.7 | coolinchen| 2013-02-28 11:50:20 | modify response code
 *           3.0.6 | coolinchen| 2012-12-05 17:10:11 | support sending a request by Post
 *           3.0.5 | coolinchen| 2012-10-08 11:20:12 | support printing request string and result
 *           3.0.4 | coolinchen| 2012-09-07 10:20:12 | support POST request in  "multipart/form-data" format
 *           3.0.3 | nemozhang | 2012-08-28 16:40:20 | support cpay callback sig verifictaion
 *           3.0.2 | sparkeli  | 2012-03-06 17:58:20 | add statistic fuction which can report API's access time and
 *                                                     number to background server
 *           3.0.1 | nemozhang | 2012-02-14 17:58:20 | resolve a bug: at line 108, change  'post' to  $method
 *           3.0.0 | nemozhang | 2011-12-12 11:11:11 | initialization
 */

namespace Tencent\QQ\Open;

use Tencent\QQ\Open\lib\SnsNetwork;
use Tencent\QQ\Open\lib\SnsSigCheck;
use Tencent\QQ\Open\lib\SnsStat;

/** 错误码定义 */
const OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY = 1801;    // 参数为空
const OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID = 1802;  // 参数格式错误
const OPENAPI_ERROR_RESPONSE_DATA_INVALID = 1803;       // 返回包格式错误
const OPENAPI_ERROR_CURL = 1900;                        // 网络错误，偏移量 1900，详见 https://curl.haxx.se/libcurl/c/libcurl-errors.html

/**
 * 提供访问腾讯开放平台 OpenAPI V3 的接口
 */
class OpenAPIV3 {

    private $appId = 0;
    private $appKey;
    private $server_name;
    private $format = 'json';
    private $stat_url = "apistat.tencentyun.com";
    private $is_stat = true;

    /**
     * 构造函数
     *
     * @param int    $appId 应用 ID
     * @param string $appKey 应用密钥
     */
    public function __construct($appId, $appKey) {
        $this->appId = $appId;
        $this->appKey = $appKey;
    }

    public function setServerName($server_name) {
        $this->server_name = $server_name;
        return $this;
    }

    public function setStatUrl($stat_url) {
        $this->stat_url = $stat_url;
        return $this;
    }

    public function setIsStat($is_stat) {
        $this->is_stat = $is_stat;
        return $this;
    }

    /**
     * 执行 API 调用，返回结果数组
     *
     * @param string       $route 调用的 API 路由，例如 /v3/user/get_info，参考 http://wiki.open.qq.com/wiki/API%E5%88%97%E8%A1%A8
     * @param array        $params 请求参数
     * @param array|string $cookie
     * @param string       $method 请求方法，默认 post
     *
     * @return array
     */
    public function api($route, $params, $cookie = '', $method = 'post') {
        // 检查 OpenID 是否为空
        if (!isset($params['openid']) || empty($params['openid'])) {
            return [
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'OpenID is empty'
            ];
        }

        // 检查 OpenID 是否合法
        if (!self::isOpenId($params['openid'])) {
            return [
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'OpenID is invalid'
            ];
        }

        // 检查请求 API 是否由左斜杠开头
        if (strpos($route, '/') !== 0) {
            return [
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'Request API route format error'
            ];
        }

        // 无需 sig, 会自动生成
        unset($params['sig']);

        // 添加一些参数
        $params['appid'] = $this->appId;
        $params['format'] = $this->format;

        // 生成签名
        $params['sig'] = SnsSigCheck::makeSig($method, $route, $params, "{$this->appKey}&");

        $url = "https://{$this->server_name}{$route}";

        // 记录接口调用开始时间
        $start_time = SnsStat::getTime();

        // 通过调用以下方法，可以打印出最终发送到 OpenAPI 服务器的请求参数以及 URL
        // self::printRequest($url, $params, $method);

        // 发起请求
        $ret = SnsNetwork::makeRequest($url, $params, $cookie, $method);

        if ($ret['result'] === false) {
            $result_array = [
                'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
                'msg' => $ret['msg'],
            ];
        } else {
            $result_array = json_decode($ret['msg'], true);
        }

        // 远程返回的不是 json 格式, 说明返回包有问题
        if (is_null($result_array)) {
            $result_array = [
                'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
                'msg' => $ret['msg']
            ];
        }

        // 统计上报
        if ($this->is_stat) {
            $stat_params = [
                'appid' => $this->appId,
                'pf' => $params['pf'],
                'rc' => $result_array['ret'],
                'svr_name' => $this->server_name,
                'interface' => $route,
                'protocol' => 'https',
                'method' => $method,
            ];
            SnsStat::statReport($this->stat_url, $start_time, $stat_params);
        }

        // 通过调用以下方法，可以打印出调用 OpenAPI 请求的返回码以及错误信息
        // self::printResponse($result_array);

        return $result_array;
    }

    /**
     * 执行上传文件 API 调用，返回结果数组
     *
     * @param string       $route 调用的 API 路由，例如 /v3/user/get_info，参考 http://wiki.open.qq.com/wiki/API%E5%88%97%E8%A1%A8
     * @param array        $params 请求参数
     * @param array        $array_files 文件，key 为 OpenAPI 接口参数，value 为“@”加上文件全路径的字符串
     *                            举例 ['pic' => '@/home/xxx/hello.jpg', ...];
     * @param array|string $cookie
     *
     * @return array 结果数组
     */
    public function apiUploadFile($route, $params, $array_files, $cookie = '') {
        // 检查 OpenID 是否为空
        if (!isset($params['openid']) || empty($params['openid'])) {
            return [
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'OpenID is empty'];
        }

        // 检查 OpenID 是否合法
        if (!self::isOpenId($params['openid'])) {
            return [
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
                'msg' => 'OpenID is invalid'];
        }

        // 检查请求 API 是否由左斜杠开头
        if (strpos($route, '/') !== 0) {
            return [
                'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
                'msg' => 'Request API route format error'
            ];
        }

        // 无需 sig, 会自动生成
        unset($params['sig']);

        // 添加一些参数
        $params['appid'] = $this->appId;
        $params['format'] = $this->format;

        // 生成签名
        $params['sig'] = SnsSigCheck::makeSig('post', $route, $params, "{$this->appKey}&");

        // 图片参数不能参与签名
        foreach ($array_files as $k => $v) {
            $params[$k] = $v;
        }

        $url = "https://{$this->server_name}{$route}";

        // 记录接口调用开始时间
        $start_time = SnsStat::getTime();

        // 通过调用以下方法，可以打印出最终发送到openapi服务器的请求参数以及url，默认注释
        // self::printRequest($url, $params, 'post');

        // 发起请求
        $ret = SnsNetwork::makeRequestWithFile($url, $params, $cookie);

        if ($ret['result'] === false) {
            $result_array = [
                'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
                'msg' => $ret['msg'],
            ];
        } else {
            $result_array = json_decode($ret['msg'], true);
        }

        // 远程返回的不是 json 格式, 说明返回包有问题
        if (is_null($result_array)) {
            $result_array = [
                'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
                'msg' => $ret['msg']
            ];
        }

        // 统计上报
        if ($this->is_stat) {
            $stat_params = [
                'appid' => $this->appId,
                'pf' => $params['pf'],
                'rc' => $result_array['ret'],
                'svr_name' => $this->server_name,
                'interface' => $route,
                'protocol' => 'https',
                'method' => 'post',
            ];
            SnsStat::statReport($this->stat_url, $start_time, $stat_params);
        }

        // 通过调用以下方法，可以打印出调用 OpenAPI 请求的返回码以及错误信息，默认注释
        // self::printResponse($result_array);

        return $result_array;
    }

    /**
     * 检查 OpenID 的格式
     *
     * @param string $openid OpenID
     *
     * @return bool
     */
    private static function isOpenId($openid) {
        return preg_match('/^[0-9a-fA-F]{32}$/', $openid) ? true : false;
    }

    /**
     * 打印出请求串的内容，当 API 中的这个函数的注释放开将会被调用。
     *
     * @param string $url 请求串内容
     * @param array  $params 请求参数
     * @param string $method 请求方法 get / post
     */
    private function printRequest($url, $params, $method) {
        $query_string = SnsNetwork::makeQueryString($params);
        if ($method == 'get') {
            $url = $url . "?" . $query_string;
        }
        echo "\n============= request info ================\n\n";
        print_r("method : " . $method . "\n");
        print_r("url    : " . $url . "\n");
        if ($method == 'post') {
            print_r("query_string : " . $query_string . "\n");
        }
        echo "\n";
        print_r("params : " . print_r($params, true) . "\n");
        echo "\n";
    }

    /**
     * 打印出返回结果的内容，当API中的这个函数的注释放开将会被调用。
     *
     * @param array $array 待打印的array
     */
    private function printResponse($array) {
        echo "\n============= response info ================\n\n";
        print_r($array);
        echo "\n";
    }
}
