<?php

namespace Bytedance\Toutiao\Token;

class Sns
{

    private $_appid;

    private $_secret;

    private $_context;

    public function __construct($appid, $secret)
    {
        if (empty($appid)) {
            throw new \Exception('请设定$appid');
        }
        if (empty($secret)) {
            throw new \Exception('请设定$secret');
        }
        $this->_appid = $appid;
        $this->_secret = $secret;

        $opts = array(
            'http' => array(
                'follow_location' => 3,
                'max_redirects' => 3,
                'timeout' => 10,
                'method' => "GET",
                'header' => "Connection: close\r\n"
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        $this->_context = stream_context_create($opts);
    }

    /**
     * code2Session
     * 通过login接口获取到登录凭证后，开发者可以通过服务器发送请求的方式获取session_key和openId。

     * 提示

     * 登录凭证 code，anonymous_code 只能使用一次，非匿名需要 code，非匿名下的 anonymous_code 用于数据同步，匿名需要 anonymous_code。

     * 接口地址
     * GET https://developer.toutiao.com/api/apps/jscode2session
     * 输入
     * 注意

     * code 和 anonymous_code 至少要有一个。

     * 名称	描述
     * appid	小程序 ID
     * secret	小程序的 APP Secret，可以在开发者后台获取
     * code	login接口返回的登录凭证
     * anonymous_code	login接口返回的匿名登录凭证
     * 输出
     * 返回值为 JSON 形式，其中包括如下字段：

     * 名称	描述
     * session_key	会话密钥，如果请求时有 code 参数才会返回
     * openid	用户在当前小程序的 ID，如果请求时有 code 参数才会返回
     * anonymous_openid	匿名用户在当前小程序的 ID，如果请求时有 anonymous_code 参数才会返回
     * 注意

     * session_key会随着login接口的调用被刷新。可以通过checkSession方法验证当前 session 是否有效，从而避免频繁登录。

     * session_key会话密钥 session_key 是对用户数据进行 加密签名 的密钥。为了应用自身的数据安全，开发者服务器不应该把会话密钥下发到小程序，也不应该对外提供这个密钥。

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
     * 40018	code 错误
     * 40019	acode 错误
     * 其它	参数为空
     *
     * @throws \Exception
     * @return array
     */
    public function getJscode2session($code = "", $anonymous_code = "")
    {
        if (empty($code) && empty($anonymous_code)) {
            throw new \Exception('code和anonymous_code都不能为空');
        }
        $response = file_get_contents("https://developer.toutiao.com/api/apps/jscode2session?appid={$this->_appid}&secret={$this->_secret}&code={$code}&anonymous_code={$anonymous_code}", false, $this->_context);
        $response = json_decode($response, true);

        return $response;
    }

    public function __destruct()
    {
    }
}
