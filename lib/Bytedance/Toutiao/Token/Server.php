<?php

/**
 * getAccessToken
 * access_token 是小程序的全局唯一调用凭据，开发者调用小程序支付时需要使用 access_token。
 * access_token 的有效期为 2 个小时，需要定时刷新 access_token，重复获取会导致之前一次获取的 access_token 的有效期缩短为 5 分钟。
 * @author guoyongrong <handsomegyr@126.com>
 *
 */

namespace Bytedance\Toutiao\Token;

class Server
{

    private $_appid = null;

    private $_secret = null;

    public function __construct($appid, $secret)
    {
        $this->_appid = $appid;
        $this->_secret = $secret;
    }

    /**
     * 接口地址
     * GET https://developer.toutiao.com/api/apps/token
     * 输入
     * 名称	描述
     * appid	小程序 ID
     * secret	小程序的 APP Secret，可以在开发者后台获取
     * grant_type	获取 access_token 时值为 client_credential
     * 输出
     * 返回值为 JSON 形式，其中包括如下字段：

     * 名称	描述
     * access_token	获取的 access_token
     * expires_in	access_token 有效时间，单位：秒
     * 当服务器端无法正确返回时，会返回如下信息：

     * 名称	数据类型	描述
     * errcode	number	错误号
     * errmsg	string	错误信息
     * 具体对应关系为：

     * 错误号	描述
     * 0	请求成功
     * -1	系统错误
     * 40015	appid 错误
     * 40017	secret 错误
     * 40020	grant_type 不是 client_credential
     * 其它	参数为空
     */
    public function getAccessToken()
    {
        $url = "https://developer.toutiao.com/api/apps/token?grant_type=client_credential&appid={$this->_appid}&secret={$this->_secret}";
        return json_decode(file_get_contents($url), true);
    }

    public function __destruct()
    {
    }
}
