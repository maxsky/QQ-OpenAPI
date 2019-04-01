<?php

use Tencent\QQ\Open\OpenApiV3;

/**
 * OpenAPI V3 SDK 示例代码，适用于大部分OpenAPI。如果是上传文件类OpenAPI，请参考本SDK包中的“Test_UploadFile.php”文件中的示例代码。
 *
 * @version 3.0.4
 * @author open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @History:
 *          3.0.4 | coolinchen | 2012-09-07 10:20:12 | initialization
 */

// 应用基本信息
$appId = 100657839;
$appKey = 'b96b85196a04ff2ef08707f43979db15';

// OpenAPI 的服务器 IP
// 最新的 API 服务器地址请参考 Wiki 文档：http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3
$server_name = '119.147.19.43';

// 用户的 OpenID/Access Token（App 返回的 Access Token 即 OpenKey）
$openId = 'E098C1E975A2459E534B48FB3224CFB6';
$accessToken = '05219DB6D7C04CA0B3F01A51D32635E3';

// 所要访问的平台, pf 的其它取值参考 Wiki文档：http://wiki.open.qq.com/wiki/API3.0%E6%96%87%E6%A1%A3
// 一般来说取用户信息用 qzone 即可
$pf = 'qzone';

$sdk = new OpenApiV3($appId, $appKey);
$sdk->setServerName($server_name);

$ret = get_user_info($sdk, $openId, $accessToken, $pf);
print_r("===========================\n");
print_r($ret);

/**
 * 获取好友资料
 *
 * @param OpenApiV3|object $sdk OpenApiV3 Object
 * @param string           $openId
 * @param string           $access_token
 * @param string           $pf 平台
 *
 * @return array 好友资料数组
 */
function get_user_info($sdk, $openId, $access_token, $pf) {
    $params = [
        'openid' => $openId,
        'openkey' => $access_token,
        'pf' => $pf
    ];

    $func_route = '/v3/user/get_info';
    return $sdk->api($func_route, $params);
}

// end of script
