<?php

/**
 * OpenAPI V3 SDK 上传文件类接口示例代码
 */

use PHPUnit\Framework\TestCase;
use Tencent\QQ\Open\OpenAPIV3;

class Test_UploadFile extends TestCase {

    // 应用基本信息
    private $appId = '';
    private $appKey = '';
    // OpenAPI 的服务器 IP
    // 最新的 API 服务器地址请参考 Wiki文档：http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3
    private $server_name = 'openapi.tencentyun.com';

    function testUploadFile() {
        $openApi = (new OpenAPIV3($this->appId, $this->appKey))->setServerName($this->server_name);
        // 所要访问的平台，pf 的其它取值参考 Wiki文档：http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3
        $params = [
            'openid' => 'User Open ID',
            'openkey' => 'Access Token',
            'pf' => 'tapp'
        ];

        $api_route = '/v3/t/add_pic_t';

        $array_files['pic'] = 'Picture Path'; // 指定图片地址

        $result = $openApi->apiUploadFile($api_route, $params, $array_files);

        $this->assertTrue($result['ret'] === 0);
    }
}
