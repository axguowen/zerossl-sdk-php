<?php
// +----------------------------------------------------------------------
// | ZeroSSL SDK [ZeroSSL SDK for PHP]
// +----------------------------------------------------------------------
// | ZeroSSL SDK
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

namespace axguowen\zerossl;

use axguowen\HttpClient;
use axguowen\httpclient\Error;

abstract class BaseClient
{
    // 请求接口根地址
    const BASE_URL = 'https://api.zerossl.com';
    
    /**
     * 配置参数
     * @var string
     */
    protected $options = [
        // API密钥
        'access_key' => '',
        // 国家
        'country_name' => 'CN',
        // 省份
        'state_or_province_name' => '',
        // 城市
        'locality_name' => '',
        // 注册人姓名
        'organization_name' => '',
        // 组织名称
        'organizational_unit_name' => '',
        // 公共名称
        'common_name' => '',
        // 邮箱地址
        'email_address' => '',
    ];

    /**
     * 配置参数
     * @var string
     */
    protected $privateKey = '';

    /**
     * 架构函数
     * @access public
     * @param $options 配置参数
     */
    public function __construct($options = [])
    {
        // 如果指定配置
        if(!empty($options)){
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * 获取证书私钥
     * @access public
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * 发送POST请求
     * @access protected
     * @param string $path 请求接口
     * @param array $body 请求参数
     * @return array
     */
    protected function post($path, $body = [])
    {
        // 构造授权参数
        $path .= (false === strpos($path, '?') ? '?' : '&') . 'access_key=' . $this->options['access_key'];
        // 发送请求
        $ret = HttpClient::post(self::BASE_URL . $path, $body);
        if (!$ret->ok()) {
            return [null, new Error($path, $ret)];
        }
        $r = ($ret->body === null) ? [] : $ret->json();
        return [$r, null];
    }

    /**
     * 发送GET请求
     * @access protected
     * @param string $path 请求接口
     * @param array $query 请求参数
     * @return array
     */
    protected function get($path, $query = [])
    {
        // 构造授权参数
        $path .= (false === strpos($path, '?') ? '?' : '&') . 'access_key=' . $this->options['access_key'];
        // 如果请求参数不为空
        if(!empty($query)){
            // 拼接请求参数
            $path .= '&' . http_build_query($query);
        }
        // 发送请求
        $ret = HttpClient::get(self::BASE_URL . $path);
        if (!$ret->ok()) {
            return [null, new \Exception($ret->error['type'])];
        }
        $r = ($ret->body === null) ? [] : $ret->json();
        return [$r, null];
    }

    /**
     * 生成证书CSR
     * @access protected
     * @param string $domain 域名
     * @return string
     */
    protected function makeCSR($domain)
    {
        // 参数
        $dn = [
            'commonName' => $domain,
        ];
        // 指定所在国家
        if(!empty($this->options['country_name'])){
            $dn['countryName'] = $this->options['country_name'];
        }
        // 指定所在省份
        if(!empty($this->options['state_or_province_name'])){
            $dn['stateOrProvinceName'] = $this->options['state_or_province_name'];
        }
        // 指定所在城市
        if(!empty($this->options['locality_name'])){
            $dn['localityName'] = $this->options['locality_name'];
        }
        // 指定注册人姓名
        if(!empty($this->options['organization_name'])){
            $dn['organizationName'] = $this->options['organization_name'];
        }
        // 指定组织名称
        if(!empty($this->options['organizational_unit_name'])){
            $dn['organizationalUnitName'] = $this->options['organizational_unit_name'];
        }
        // 指定公共名称
        if(!empty($this->options['common_name'])){
            $dn['commonName'] = $this->options['common_name'];
        }
        // 指定邮箱地址
        if(!empty($this->options['email_address'])){
            $dn['emailAddress'] = $this->options['email_address'];
        }

        // 配置
        $config = [
            // 字节数 512 1024 2048 4096 等
            'private_key_bits' => 2048,
            //加密类型
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            // 签名哈希算法
            'digest_alg' => 'sha256',
        ];

        // 生成新的私钥(或公钥)对
        $privkey = openssl_pkey_new($config);
        // 生成证书签名请求
        $csr = openssl_csr_new($dn, $privkey, $config);
        // 将CSR存储到一个变量 $csrout
        openssl_csr_export($csr, $csrout);
        // 将私钥存储到一个变量 $pkeyout
        openssl_pkey_export($privkey, $pkeyout, null, $config);
        // 存储私钥
        $this->privateKey = $pkeyout;
        // 返回
        return $csrout;
    }
}
