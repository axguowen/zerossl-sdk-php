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

namespace axguowen;

use axguowen\zerossl\BaseClient;

class ZeroSSL extends BaseClient
{
    /**
     * 创建证书申请
     * @access public
     * @param string $domains 域名, 多个用英文逗号隔开
     * @param int $validityDays 证书有效期, 仅支持365和90
     * @param int $strictDomains 严格模式
     * @param string $replacementForCertificate 要替换的证书的证书哈希
     * @return array
     */
    public function createCertificate($domains, $validityDays = 90, $strictDomains = null, $replacementForCertificate = null)
    {
        // 请求体
        $body = [
            'certificate_domains' => $domains,
            'certificate_csr' => $this->makeCSR($domains),
            'certificate_validity_days' => $validityDays,
        ];
        if(!is_null($strictDomains)){
            $body['strict_domains'] = $strictDomains;
        }
        if(!is_null($replacementForCertificate)){
            $body['replacement_for_certificate'] = $replacementForCertificate;
        }
        // 发送请求
        return $this->post('/certificates', $body);
    }

    /**
     * 验证域名
     * @access public
     * @param string $id 证书ID/hash
     * @param string $method 验证方式, 可选值: EMAIL, CNAME_CSR_HASH, HTTP_CSR_HASH, HTTPS_CSR_HASH
     * @param string $email 验证邮箱, 使用邮箱验证时必填
     * @return array
     */
    public function verifyDomains($id, $method = 'HTTP_CSR_HASH', $email = null)
    {
        // 请求体
        $body = [
            'validation_method' => $method
        ];
        // 如果使用邮箱验证
        if($body['validation_method'] == 'EMAIL'){
            $body['validation_email'] = $email;
        }
        // 发送请求
        return $this->post('/certificates/' . $id . '/challenges', $body);
    }

    /**
     * 下载证书
     * @access public
     * @param string $id 证书ID/hash
     * @param string $zipFile 是否下载压缩包
     * @param string $includeCrossSigned 是否包含交叉签名证书
     * @return array
     */
    public function downloadCertificate($id, $zipFile = false, $includeCrossSigned = 0)
    {
        // 路径
        $path = '/certificates/' . $id . '/download';
        // 如果不是zip下载
        if(!$zipFile){
            $path .= '/return';
        }
        // 请求参数
        $query = [];
        if($includeCrossSigned == 1){
            $query['include_cross_signed'] = $includeCrossSigned;
        }
        // 发送请求
        return $this->get($path, $query);
    }

    /**
     * 获取指定证书信息
     * @access public
     * @param string $id 证书ID/hash
     * @return array
     */
    public function getCertificate($id)
    {
        // 发送请求
        return $this->get('/certificates/' . $id);
    }

    /**
     * 获取证书列表
     * @access public
     * @param array $options 请求参数
     * @return array
     */
    public function listCertificates($options = [])
    {
        // 发送请求
        return $this->get('/certificates', $options);
    }

    /**
     * 获取域名验证状态
     * @access public
     * @param string $id 证书ID/hash
     * @return array
     */
    public function verificationStatus($id)
    {
        // 发送请求
        return $this->get('/certificates/' . $id . '/status');
    }

    /**
     * 重新发送验证邮件
     * @access public
     * @param string $id 证书ID/hash
     * @return array
     */
    public function resendVerification($id)
    {
        // 发送请求
        return $this->post('/certificates/' . $id . '/challenges/email');
    }

    /**
     * 吊销证书
     * @access public
     * @param string $id 证书ID/hash
     * @return array
     */
    public function revokeCertificate($id)
    {
        // 发送请求
        return $this->post('/certificates/' . $id . '/revoke');
    }

    /**
     * 取消证书申请
     * @access public
     * @param string $id 证书ID/hash
     * @return array
     */
    public function cancelCertificate($id)
    {
        // 发送请求
        return $this->post('/certificates/' . $id . '/cancel');
    }

    /**
     * 验证CSR
     * @access public
     * @param string $csr 证书CSR
     * @return array
     */
    public function validateCSR($csr)
    {
        // 请求体
        $body = [
            'csr' => $csr
        ];
        // 发送请求
        return $this->post('/validation/csr', $body);
    }
}
