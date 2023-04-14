# ZeroSSL SDK for PHP

一个基于PHP的ZeroSSL SDK


## 安装
~~~
composer require axguowen/zerossl-sdk-php
~~~

## 用法示例

创建证书申请订单

~~~php

use axguowen\ZeroSSL;

// 配置
$config = [
    'access_key' => '********'
];
// 实例化
$zeroSSL = new \axguowen\ZeroSSL($config);

// 创建证书申请订单
$createCertificate = $zeroSSL->createCertificate('xxx.example.com');

// 创建失败
if(is_null($createCertificate[0])){
    throw new \Exception($createCertificate[1]);
}
// 证书申请订单信息
var_dump($createCertificate[0]);

// 获取私钥
$privateKey = $zeroSSL->getPrivateKey();
var_dump($privateKey);

~~~


验证域名

~~~php

use axguowen\ZeroSSL;

// 配置
$config = [
    'access_key' => '********'
];
// 实例化
$zeroSSL = new \axguowen\ZeroSSL($config);

// 验证域名
$verifyDomains = $zeroSSL->verifyDomains($domainRow['zerossl_id']);
// 创建失败
if(is_null($verifyDomains[0])){
    throw new \Exception($verifyDomains[1]);
}
// 证书申请订单信息
var_dump($verifyDomains[0]);

~~~

更多功能请查看ZeroSSL类源码