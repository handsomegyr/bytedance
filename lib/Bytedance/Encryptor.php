<?php

namespace Bytedance;

/**
 * Class Encryptor
 */
class Encryptor
{
    /**
     * block size
     *
     * @var int
     */
    protected $blockSize = 16;

    /**
     * Encrypt method
     *
     * @var string
     */
    protected $method = 'AES-128-CBC';

    /**
     * Encrypt options
     *
     * @var int
     */
    protected $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

    /**
     * PKCS#7 pad.
     *
     * @param string $text
     * @param int    $blockSize
     *
     * @return string
     * @throws Exception
     */
    public function pkcs7Pad(string $text, int $blockSize)
    {
        if ($blockSize > 256) {
            throw new \Exception('$blockSize may not be more than 256');
        }
        $padding = $blockSize - (strlen($text) % $blockSize);
        $pattern = chr($padding);

        return $text . str_repeat($pattern, $padding);
    }

    /**
     * PKCS#7 unpad.
     *
     * @param string $text
     *
     * @return string
     */
    public function pkcs7Unpad(string $text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > $this->blockSize) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * Decrypt data.
     *
     * @param string $sessionKey
     * @param string $iv
     * @param string $encrypted
     *
     * @return array
     * @throws Exception
     */
    public function decryptData(string $sessionKey, string $iv, string $encrypted)
    {
        $plainText = openssl_decrypt(
            base64_decode($encrypted),
            $this->method,
            base64_decode($sessionKey),
            $this->options,
            base64_decode($iv)
        );

        $decryptData = json_decode($this->pkcs7Unpad($plainText), true);

        if ($decryptData == false) {
            throw new \Exception('The given payload is invalid.');
        }

        return $decryptData;
    }

    /**
     * Encrypt data.
     *
     * @param string $sessionKey
     * @param string $iv
     * @param array  $data
     * @return string
     * @throws \Exception
     */
    public function encryptData(string $sessionKey, string $iv, $data = [])
    {
        // 反加密字节跳动小程序获取到的授权信息，发现php json_encode出来的json字符串与解密得到的不一致，要特殊处理
        $str = str_replace('\\', '', json_encode($data, JSON_UNESCAPED_UNICODE));

        $plainText = $this->pkcs7Pad($str, $this->blockSize);

        $encryptText = openssl_encrypt(
            $plainText,
            $this->method,
            base64_decode($sessionKey),
            $this->options,
            base64_decode($iv)
        );
        return base64_encode($encryptText);
    }
}
