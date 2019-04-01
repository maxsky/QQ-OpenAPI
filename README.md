# QQ Open API SDK

<a href="https://996.icu"><img src="https://img.shields.io/badge/link-996.icu-red.svg"></a>

QQ 开放平台 Open API，引用自官方 SDK v3.0.9[2013-05-30]

## 安装

`composer require maxsky/qq-openapi`

## 说明

通过 **Composer** 安装后，通过如下代码实例化调用：

```php
$openApi = (new OpenApiV3('App ID', 'App Key'))->setServerName('openapi.tencentyun.com');
```

其中，`AppId `及 `AppKey `需在 [腾讯开放平台 - 管理中心](http://op.open.qq.com/manage_centerv2/) 添加应用后获取。

**注意：双平台 App（Android & iOS）不要分别从左边的安卓/iOS 菜单中添加，正确的操作是添加 Android 应用后，点击已上线 App 进入应用详情页，从详情页右上角的“平台信息”添加另一个 App。只有这样添加的两个 App 得到的 App ID 和 App Key 才会一致，否则会出现两个不同的 App ID 和 App Key。**



### 例子：获取用户信息

```php
// 请求的接口路由地址一定是左斜杠 '/' 开头
$openApi->api('/v3/user/get_info', [
		'openkey' => 'Access Token',
		'openid' => 'OpenID',
		'pf' => 'qzone'
]);
```

解释一下：`openkey` 为 App 唤起 QQ，用户点击允许登录后返回的 **Access Token**，**OpenID** 也是 App 返回的，`pf` 一般来说填写 `qzone` 即可。

返回值参考：[v3/user/get_info - 2.7 返回参数说明 - 腾讯开放平台](http://wiki.open.qq.com/wiki/v3/user/get_info#2.7.09.E8.BF.94.E5.9B.9E.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E)

返回值类型：`array`