<?php

/**
 * 接口调用凭证
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
     * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/interface-request-credential/get-access-token
     * getAccessToken更新时间：2022-07-22 15:52:17
     *
     * 为了保障应用的数据安全，只能在开发者服务器使用 AppSecret，如果小程序存在泄露 AppSecret 的问题，字节小程序平台将有可能下架该小程序，并暂停该小程序相关服务。
     *
     *
     * Bug & Tip
     * Tip: token 是小程序级别 token，不要为每个用户单独分配一个 token，会导致 token 校验失败。建议每小时更新一次即可。
     */
    public function getAccessToken()
    {
        $url = "https://developer.toutiao.com/api/apps/v2/token";
        $params = array(
            'appid' => $this->_appid,
            'secret' => $this->_secret,
            'grant_type' => 'client_credential'
        );
        $request = new \Bytedance\Http\Request();
        $resp = $request->postByfileGetContents($url, $params);
        return json_decode($resp, true);
    }
}
