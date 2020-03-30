<?php

namespace Bytedance;

/**
 * Defines a few helper methods.
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Helpers
{

    /**
     * 检测一个字符串否为Json字符串
     *
     * @param string $string            
     * @return true/false
     *
     */
    public static function isJson($string)
    {
        if (strpos($string, "{") !== false) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        } else {
            return false;
        }
    }

    /**
     * 获取随机字符串
     *
     * @param number $length            
     * @return string
     */
    public static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 作用：array转xml
     */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 作用：将xml转为array
     */
    public static function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        $object = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return @json_decode(preg_replace('/{}/', '""', @json_encode($object)), 1);
    }

    /**
     * 用户登录态签名
     * 部分服务端 API 需要校验用户登录态签名。开发者在调用服务端 API 时，需提供以 session_key 为秘钥的签名，开发者需要先使用 session_key 获取方法接口获取用户登录态。

     * 签名算法
     * 签名算法 sig_method 指的是用户态签名的编码方法，目前仅支持 sig_method=hmac_sha256。

     * 开发者生成签名的方法如下，其中 post_body 指的是 post 请求的 body 部分。

     * crypto
     *   .createHmac("sha256", session_key)
     *   .update(JSON.stringify(post_body))
     *   .digest("hex");

     * 示例
     * 假设开发者 post 请求的body部分为{"foo":"bar"}，session_key为"724edcafc423d167724edcbe"，则计算可得开发者签名为：

     * 44b5092fa1c9adba03803239934d4958b8a1840adf0cee8d5e95c1cf5d495e0e
     */
    public static function signature4SessionKey(
        string $rawData,
        string $sessionKey,
        $method = 'hmac_sha256'
    ) {
        if ($method == 'sha1') {
            return sha1($rawData . $sessionKey);
        } elseif ($method == 'hmac_sha256') {
            return hash_hmac('sha256', $rawData, $sessionKey);
        } else {
            throw new \Exception("签名算法{$method}未实现");
        }
    }

    /**
     * 服务端数据签名
     *
     * @param       $data
     * @param array $expectKeys
     * @param       $app_secret
     * @return string
     */
    public static function signature4Data($data, $expectKeys = [], $app_secret)
    {
        ksort($data);

        foreach ($data as $key => $value) {
            if (empty($value) || $key == 'sign' || $key == 'risk_info' || in_array($key, $expectKeys)) {
                unset($data[$key]);
            }

            if (!is_string($value)) {
                $data[$key] = \json_encode($value);
            }
        }

        return md5(urldecode(http_build_query($data)) . $app_secret);
    }
}
