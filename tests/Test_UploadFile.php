<?php

/**
 * OpenAPI V3 SDK 上传文件类接口示例代码
 */

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Tencent\QQ\Open\OpenAPIv3;

class Test_UploadFile extends TestCase {

    // 应用基本信息
    private $appId = '';
    private $appKey = '';
    // OpenAPI 的服务器 IP
    // 最新的 API 服务器地址请参考 Wiki文档：https://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3#OpenAPI_V3.0.E8.B0.83.E7.94.A8.E8.AF.B4.E6.98.8E
    private $server_name = 'https://openapi.tencentyun.com';

    public function testUploadFile() {
        $openApi = (new OpenAPIv3($this->appId, $this->appKey))->setServerName($this->server_name);
        // 所要访问的平台，pf 的其它取值参考 Wiki文档：https://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3#.E5.85.AC.E5.85.B1.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E
        $params = [
            'openkey' => 'Access Token',
            'openid' => 'Open ID',
            'pf' => 'tapp'
        ];

        $route = '/v3/t/add_pic_t';

        $files['pic'] = 'Picture Path'; // 指定图片地址

        try {
            $result = $openApi->apiUploadFile($route, $params, $files);
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
