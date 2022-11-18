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
     * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/log-in/code-2-session
     * code2Session更新时间：2022-09-27 21:15:59
     *
     * 为了保障应用的数据安全，只能在开发者服务器使用 AppSecret；开发者服务器不应该把会话密钥下发到小程序，也不应该对外提供这个密钥。如果小程序存在泄露 AppSecret 或会话密钥的问题，字节小程序平台将有可能下架该小程序，并暂停该小程序相关服务。
     *
     * 通过login接口获取到登录凭证后，开发者可以通过服务器发送请求的方式获取 session_key 和 openId。
     *
     * Tip：登录凭证 code，anonymous_code 只能使用一次，非匿名需要 code，非匿名下的 anonymous_code 用于数据同步，匿名需要 anonymous_code。
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
