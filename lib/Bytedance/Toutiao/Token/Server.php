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
     * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/interface-request-credential/get-access-token
     * getAccessToken更新时间：2021-09-10 14:48:49
     * access_token 是小程序的全局唯一调用凭据，开发者调用小程序支付时需要使用 access_token。
     * access_token 的有效期为 2 个小时，需要定时刷新 access_token，重复获取会导致之前一次获取的 access_token 的有效期缩短为 5 分钟。
     *
     * 请求地址
     * POST https://developer.toutiao.com/api/apps/v2/token
     *
     * 请求参数
     * 属性数据类型 说明
     *
     * appid string 小程序 ID
     *
     * secret string 小程序的 APP Secret，可以在开发者后台获取
     *
     * grant_type string 获取 access_token 时值为 client_credential
     *
     * 返回值
     * 返回值为 JSON 形式，其中包括如下字段：
     *
     * 属性 数据类型 说明
     *
     * access_token string 获取的 access_token
     *
     * expires_in int64 access_token 有效时间，单位：秒
     *
     * errCode 当服务器端无法正确返回时，会返回如下信息：
     *
     * 属性 数据类型 说明
     *
     * err_no int64 错误码
     *
     * err_tips string 错误信息
     *
     * 具体对应关系为：详细错误号 描述
     *
     * 0 请求成功
     *
     * -1 系统错误
     *
     * 40015 appid 错误
     *
     * 40017 secret 错误
     *
     * 40020 grant_type 不是 client_credential
     *
     * 其它 参数为空
     *
     * 请求示例
     * POST 请求 body
     *
     * {
     * "appid": "ttabc*****",
     * "secret": "d428***********",
     * "grant_type": "client_credential"
     * }
     *
     * 返回示例
     * 正常返回
     *
     * {
     * "err_no": 0,
     * "err_tips": "success",
     * "data": {
     * "access_token": "0801121***********",
     * "expires_in": 7200
     * }
     * }
     *
     * 错误返回
     *
     * {
     * "err_no": 40017,
     * "err_tips": "bad secret",
     * "data": {
     * "access_token": "",
     * "expires_in": 0
     * }
     * }
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
