<?php

/**
 * OpenAPI V3 SDK 获取 QQ 用户信息示例代码
 */

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Tencent\QQ\Open\OpenAPIv3;

class Test_OpenAPI extends TestCase {

    // 应用基本信息
    private $appId = '';
    private $appKey = '';
    // OpenAPI 的服务器 IP
    // 最新的 API 服务器地址请参考 Wiki 文档：https://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3#OpenAPI_V3.0.E8.B0.83.E7.94.A8.E8.AF.B4.E6.98.8E
    private $server_name = 'https://openapi.tencentyun.com';

    public function testGetUserInfo() {
        $openApi = (new OpenAPIv3($this->appId, $this->appKey))->setServerName($this->server_name);
        // pf 的其它值参考 Wiki文档：https://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3#.E5.85.AC.E5.85.B1.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E
        // 一般取用户信息用 qzone 即可
        $params = [
            'openkey' => 'Access Token',
            'openid' => 'User Open ID',
            'pf' => 'qzone'
        ];

        $route = '/v3/user/get_info';


        try {
            $result = $openApi->api($route, 'POST', $params, []);
        } catch (Throwable $e) {
            print_r('Error Message: ' . $e->getMessage());
            print_r('Error Code: ' . $e->getCode());

            if ($e instanceof ClientException) {
                print_r($e->getResponse());
            } elseif ($e instanceof BadResponseException) {
                print_r(json_decode($e->getResponse()->getBody(), true));
            }

            die;
        }

        $this->assertTrue($result['ret'] === 0);
    }
}
