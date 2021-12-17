<?php

namespace Bytedance\Toutiao\Token;

/**
 * 登录
 * 
 * @author guoyongrong <handsomegyr@126.com>
 *        
 */
class Sns
{
    private $_appid;
    private $_secret;
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
    }

    /**
     * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/log-in/code-2-session
     * code2Session更新时间：2021-09-13 15:19:42
     * 通过login接口获取到登录凭证后，开发者可以通过服务器发送请求的方式获取 session_key 和 openId。
     *
     * Tip：登录凭证 code，anonymous_code 只能使用一次，非匿名需要 code，非匿名下的 anonymous_code 用于数据同步，匿名需要 anonymous_code。
     * 请求地址
     * POST https://developer.toutiao.com/api/apps/v2/jscode2session
     *
     * 请求参数
     * 属性 数据类型 说明
     *
     * appid string 小程序 ID
     *
     * secret string 小程序的 APP Secret，可以在开发者后台获取
     *
     * code string login 接口返回的登录凭证
     *
     * anonymous_code string login 接口返回的匿名登录凭证
     *
     * Tip：code 和 anonymous_code 至少要有一个。
     * 返回值
     * 返回值为 JSON 形式，其中包括如下字段：
     *
     * 属性 数据类型 说明
     *
     * session_key string 会话密钥，如果请求时有 code 参数才会返回
     *
     * openid string 用户在当前小程序的 ID，如果请求时有 code 参数才会返回
     *
     * anonymous_openid string 匿名用户在当前小程序的 ID，如果请求时有 anonymous_code 参数才会返回
     *
     * unionid string 用户在小程序平台的唯一标识符，请求时有 code 参数才会返回。如果开发者拥有多个小程序，可通过 unionid 来区分用户的唯一性。
     *
     * Tip：对于同一个用户，不同的宿主或不同的开发者得到的 unionid 是不同的。
     * Tip：session_key 会随着login接口的调用被刷新。可以通过checkSession方法验证当前 session 是否有效，从而避免频繁登录。
     * Tip：session_key 会话密钥 session_key 是对用户数据进行 加密签名 的密钥。为了应用自身的数据安全，开发者服务器不应该把会话密钥下发到小程序，也不应该对外提供这个密钥。
     *
     * errCode 当服务器端无法正确返回时，会返回如下信息：
     *
     * 属性 数据类型 说明
     *
     * err_no int64 错误码
     *
     * err_tips string 错误信息
     *
     * 具体对应关系为：
     *
     * 错误号 描述
     *
     * 0 请求成功
     *
     * -1 系统错误
     *
     * 40014 未传必要参数，请检查
     *
     * 40015 appid 错误
     *
     * 40017 secret 错误
     *
     * 40018 code 错误
     *
     * 40019 acode 错误
     *
     * 其它 参数为空
     *
     * 请求示例
     * POST 请求 body
     * {
     * "appid": "ttabc****",
     * "secret": "d428**************7",
     * "anonymous_code": "",
     * "code": "iOyVA5hc*******"
     * }
     *
     * 返回示例
     * 正确返回
     *
     * {
     * "err_no": 0,
     * "err_tips": "success",
     * "data": {
     * "session_key": "hZy6t19VPjFqm********",
     * "openid": "V3WvSshYq9******",
     * "anonymous_openid": "",
     * "unionid": "f7510d9ab***********"
     * }
     * }
     *
     * 错误返回
     *
     * {
     * "err_no": 40015,
     * "err_tips": "bad appid",
     * "data": {
     * "session_key": "",
     * "openid": "",
     * "anonymous_openid": "",
     * "unionid": ""
     * }
     * }
     *
     * 匿名 openid 数据迁移
     */
    public function getJscode2session($code = "", $anonymous_code = "")
    {
        if (empty($code) && empty($anonymous_code)) {
            throw new \Exception('code和anonymous_code都不能为空');
        }

        $url = "https://developer.toutiao.com/api/apps/v2/jscode2session";
        $params = array(
            'appid' => $this->_appid,
            'secret' => $this->_secret,
            'anonymous_code' => $anonymous_code,
            'code' => $code
        );
        $request = new \Bytedance\Http\Request();
        $resp = $request->postByfileGetContents($url, $params);
        return json_decode($resp, true);
    }
}
