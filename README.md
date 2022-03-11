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

其中，`AppId `及 `AppKey `需在 [腾讯应用开放平台 - 我的应用](https://app.open.qq.com/p/app/list) 添加应用后获取。

### 例子：获取用户信息

```php
// 请求的接口路由地址一定是左斜杠 '/' 开头
$result = $openApi->api('/v3/user/get_info', 'POST',[
    'openkey' => 'Access Token',
    'openid' => 'OpenID',
    'pf' => 'qzone'
]);

/** @var string $result */
print_r($result);

// 这里直接封装了一个获取用户信息方法
$result = (new \Tencent\QQ\Open\UserAPI('AppID', 'AppKey'))->getInfo('AccessToken', 'OpenID');

/** @var string $result */
print_r($result);
```

解释一下：`openkey` 为客户端（App 等）唤起 QQ，用户点击允许登录后返回的 **Access Token**，**OpenID** 也是，`pf` 一般来说填写 `qzone` 即可。

返回值参考：[v3/user/get_info - 2.7 返回参数说明 - 腾讯开放平台](https://wiki.open.qq.com/wiki/v3/user/get_info#2.7.09.E8.BF.94.E5.9B.9E.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E)

