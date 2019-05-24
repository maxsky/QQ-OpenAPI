<?php

/**
 * OpenAPI V3 SDK 获取 QQ 用户信息示例代码
 */
use PHPUnit\Framework\TestCase;
use Tencent\QQ\Open\OpenAPIV3;

class Test_OpenAPI extends TestCase {

    // 应用基本信息
    private $appId = '';
    private $appKey = '';
    // OpenAPI 的服务器 IP
    // 最新的 API 服务器地址请参考 Wiki 文档：http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3
    private $server_name = 'openapi.tencentyun.com';

    function testGetUserInfo() {
        $openApi = (new OpenAPIV3($this->appId, $this->appKey))->setServerName($this->server_name);
        // pf 的其它值参考 Wiki文档：http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3
        // 一般来说取用户信息用 qzone 即可
        $params = [
            'openid' => 'User Open ID',
            'openkey' => 'Access Token',
            'pf' => 'qzone'
        ];

        $api_route = '/v3/user/get_info';

        $result = $openApi->api($api_route, $params);

        $this->assertTrue($result['ret'] === 0);
    }
}