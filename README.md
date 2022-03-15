# QQ Open API SDK

<a href="https://996.icu"><img src="https://img.shields.io/badge/link-996.icu-red.svg"></a>

QQ 开放平台 Open API，修改自官方 SDK v3.0.9[2013-05-30]

## 安装

`composer require maxsky/qq-openapi`

## 说明

通过 **Composer** 安装后，通过如下代码实例化调用：

```php
// Server Name 默认为正式，测试时使用 https://openapi.sparta.html5.qq.com
$openApi = (new OpenAPIv3('AppID', 'AppKey'))->setServerName('https://openapi.tencentyun.com');
```

其中，`AppID` 及 `AppKey` 需在 [腾讯应用开放平台 - 我的应用](https://app.open.qq.com/p/app/list) 添加应用后获取。

### 例子：获取用户信息

```php
// 请求的接口路由地址一定是左斜杠 '/' 开头
$route = '/v3/user/get_info';

try {
    $result = $openApi->api($route, 'POST', $params);
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

/** @var string $result */
print_r($result);
```

解释一下：`openkey` 为客户端（App 等）唤起 QQ，用户点击允许登录后返回的 **Access Token**，**OpenID** 也是，`pf` 一般来说填写 `qzone` 即可。

返回值参考：[v3/user/get_info - 腾讯开放平台帮助文档](https://wikinew.open.qq.com/index.html#/iwiki/931122987)



## 请求头

参考 GuzzleHttp 的 `options` 参数设置：

```php
$result = $openApi->api('/route/path', 'POST', [
    'param1' => 'value1',
    'param2' => 'value2'
], [
    'Accept' => 'application/json',
    'Content-Type' => 'application/x-www-form-urlencoded'
]);
```

