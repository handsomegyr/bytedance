#字节跳动SDK

[![MIT](https://img.shields.io/packagist/l/doctrine/orm.svg)](https://github.com/handsomegyr/bytedance/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/handsomegyr/bytedance.svg?branch=master)](https://travis-ci.org/handsomegyr/bytedance)
[![Coverage](https://img.shields.io/codecov/c/github/handsomegyr/bytedance/master.svg)](https://codecov.io/gh/handsomegyr/bytedance)
[![Packagist](https://img.shields.io/packagist/v/handsomegyr/bytedance.svg)](https://packagist.org/packages/handsomegyr/bytedance)

## Requirement

1. PHP >= 5.5
2. **[Composer](https://getcomposer.org/)**

## functions

- [x] 小程序登录
- [x] 授权信息解密
- [x] 发送模板消息
- [x] 获取小程序二维码
- [x] 设置数据缓存
- [x] 删除数据缓存
- [x] 内容安全检查
- [x] 服务端数据签名

## Installation

```shell
$ composer require "handsomegyr/bytedance" -vvv
```

## Usage

基本使用（以服务端为例）:

```php
<?php
// 获取ACCESS TOKEN
$objTokenServer = new \Bytedance\Toutiao\Token\Server($appid, $secret);
$access_token = $objTokenServer->getAccessToken();

// 创建客户端对象
$client = new \Bytedance\Toutiao\Client();
$client->setAccessToken($access_token);

// 获取二维码
$ret = $client->getQrcodeManager->create("toutiao", $path = "", 430);
print_r($ret);

//设置数据缓存
$openId = 'openid';
$sessionKey = 'session_key';
$kvList = [
    ['key' => 'custom-key', 'value' => 'custom-value']
];
$ret = $client->getUserStorageManager->setUserStorage($openId, $sessionKey, $kvList);
print_r($ret);

//删除数据缓存

$openId = 'openid';
$sessionKey = 'session_key';
$keys = ['custom_key'];
$ret = $client->getUserStorageManager->removeUserStorage($openId, $sessionKey, $keys);

//服务端数据签名
$data = [
    'app_id' => '800000000001',
    'merchant_id' => '1900000001',
    'timestamp' => 1570694312,
    'sign_type' => 'MD5',
    'out_order_no' => '201900000000000001',
    'total_amount' => 1,
    'product_code' => 'pay',
    'payment_type' => 'direct',
    'trade_type' => 'H5',
    'version' => '2.0',
    'currency' => 'CNY',
    'subject' => '测试订单',
    'body' => '测试订单',
    'uid' => '0000000000000001',
    'trade_time' => 1570585744,
    'valid_time' => 300,
    'notify_url' => '',
    'risk_info' => '{"ip":"120.230.0.0"}',
    'wx_type' => 'MWEB',
    'wx_url' => 'https://wx.tenpay.com/xxx',
    'alipay_url' => 'app_id=2019000000000006&biz_content=xxxx'
];
$signature = \Bytedance\Helpers::signature4Data($data);
echo $signature;
```

## Documentation



## License

MIT
