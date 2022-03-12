<?php

/**
 * PHP SDK for OpenAPI V3
 *
 * @version   3.0.9
 * @author    open.qq.com
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

use GuzzleHttp\Exception\GuzzleException;
use Tencent\QQ\Exceptions\QQAPIRouteException;
use Tencent\QQ\Exceptions\QQOpenIDException;
use Tencent\QQ\Exceptions\QQResponseException;
use Tencent\QQ\Lib\SnsNetwork;
use Tencent\QQ\Lib\SnsStat;
use Tencent\QQ\Utils\ParamUtil;
use Tencent\QQ\Utils\SignatureUtil;

/**
 * 提供访问腾讯开放平台 OpenAPI v3 的接口
 */
class OpenAPIv3 {

    private $appId;
    private $appKey;
    private $server_name = 'https://openapi.tencentyun.com';
    private $format = 'json';
    private $stat_url = 'apistat.tencentyun.com';
    private $is_stat = true;

    /**
     * 构造函数
     *
     * @param string $appId  应用 ID
     * @param string $appKey 应用密钥
     */
    public function __construct(string $appId, string $appKey) {
        $this->appId = $appId;
        $this->appKey = $appKey;
    }

    /**
     * @param string $server_name
     *
     * @return $this
     */
    public function setServerName(string $server_name): OpenAPIv3 {
        $this->server_name = $server_name;

        return $this;
    }

    /**
     * @param string $stat_url
     *
     * @return $this
     */
    public function setStatUrl(string $stat_url): OpenAPIv3 {
        $this->stat_url = $stat_url;

        return $this;
    }

    /**
     * @param bool $is_stat
     *
     * @return $this
     */
    public function setStat(bool $is_stat): OpenAPIv3 {
        $this->is_stat = $is_stat;

        return $this;
    }

    /**
     * 执行 API 调用，返回结果数组
     *
     * @url https://wikinew.open.qq.com/index.html#/iwiki/877913446
     *
     * @param string $route   调用的 API 路由
     * @param string $method  请求方法
     * @param array  $params  请求参数
     * @param array  $headers 请求头
     *
     * @return array
     * @throws GuzzleException
     * @throws QQAPIRouteException
     * @throws QQOpenIDException
     * @throws QQResponseException
     */
    public function api(string $route, string $method, array $params, array $headers = []): array {
        ParamUtil::openIdValidate($params['openid'] ?? null);
        ParamUtil::routeValidate($route);

        // 添加参数
        $params['appid'] = $this->appId;
        $params['format'] = $this->format;

        // 生成签名
        $params['sig'] = SignatureUtil::makeSig($route, $method, $params, "$this->appKey&");

        $url = "$this->server_name$route";

        // 记录接口调用开始时间
        $start_time = SnsStat::getTime();

        // 通过调用以下方法，可以打印出最终发送到 OpenAPI 服务器的请求参数以及 URL
        // PrintUtil::printRequest($url, $method, $params);

        // 发起请求
        $result = SnsNetwork::makeRequest($url, $method, $params, $headers);

        if (empty($result)) {
            throw new QQResponseException('Response is not json type', ERROR_RESPONSE_DATA_INVALID);
        }

        // 统计上报
        if ($this->is_stat) {
            $statParams = [
                'appid' => $this->appId,
                'pf' => $params['pf'],
                'rc' => $result['ret'],
                'svr_name' => $this->server_name,
                'interface' => $route,
                'protocol' => 'https',
                'method' => $method,
            ];

            SnsStat::statReport($this->stat_url, $start_time, $statParams);
        }

        // 通过调用以下方法，可以打印出调用 OpenAPI 请求的返回码以及错误信息
        // PrintUtil::printResponse($result);

        return $result;
    }

    /**
     * 执行上传文件 API 调用，返回结果数组
     *
     * @url https://wikinew.open.qq.com/index.html#/iwiki/877913446
     *
     * @param string $route             调用的 API 路由
     * @param array  $params            请求参数
     * @param array  $files             文件，key 为 OpenAPI 接口参数，value 为“@”加上文件全路径的字符串
     *                                  举例 ['pic' => '@/home/xxx/hello.jpg', ...];
     * @param array  $headers
     *
     * @return array
     * @throws GuzzleException
     * @throws QQAPIRouteException
     * @throws QQOpenIDException
     * @throws QQResponseException
     */
    public function apiUploadFile(string $route, array $params, array $files, array $headers = []): array {
        ParamUtil::openIdValidate($params['openid'] ?? null);
        ParamUtil::routeValidate($route);

        // 添加参数
        $params['appid'] = $this->appId;
        $params['format'] = $this->format;

        // 生成签名
        $params['sig'] = SignatureUtil::makeSig($route, 'POST', $params, "$this->appKey&");

        // 图片参数不能参与签名
        foreach ($files as $k => $v) {
            $params[$k] = $v;
        }

        $url = "$this->server_name$route";

        // 记录接口调用开始时间
        $start_time = SnsStat::getTime();

        // 通过调用以下方法，可以打印出最终发送到 OpenAPI 服务器的请求参数以及 URL
        // PrintUtil::printRequest($url, $method, $params);

        // 发起请求
        $result = SnsNetwork::makeRequestWithFile($url, $params, $headers);

        if (empty($result)) {
            throw new QQResponseException('Response is not json type', ERROR_RESPONSE_DATA_INVALID);
        }

        // 统计上报
        if ($this->is_stat) {
            $statParams = [
                'appid' => $this->appId,
                'pf' => $params['pf'],
                'rc' => $result['ret'],
                'svr_name' => $this->server_name,
                'interface' => $route,
                'protocol' => 'https',
                'method' => 'post',
            ];

            SnsStat::statReport($this->stat_url, $start_time, $statParams);
        }

        // 通过调用以下方法，可以打印出调用 OpenAPI 请求的返回码以及错误信息
        // PrintUtil::printResponse($result);

        return $result;
    }
}
