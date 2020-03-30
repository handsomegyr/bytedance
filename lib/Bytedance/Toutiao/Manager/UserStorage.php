<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 云存储服务
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class UserStorage
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/apps/';

    /**
     * 字节跳动客户端
     *
     * @var Client
     */
    private $_client;

    private $_request;

    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * setUserStorage
     * 以 key-value 形式上报用户数据到字节跳动的云存储服务。

     * 接口地址
     * POST https://developer.toutiao.com/api/apps/set_user_storage
     * 输入
     * 名称	描述
     * access_token	服务端 API 调用标识，获取方法
     * openid	登录用户唯一标识
     * signature	用户登录态签名，参考用户登录态签名算法
     * sig_method	用户登录态签名的编码方法，参考用户登录态签名算法
     * kv_list	(body 中) 要设置的用户数据
     * 输出
     * 返回值为 JSON 形式。

     * 当服务器端正确返回时，会返回如下信息：

     * 名称	数据类型	取值
     * errcode	number	0
     * 当服务器端无法正确返回时，会返回如下信息：

     * 名称	数据类型	描述
     * errcode	number	错误号
     * errmsg	string	错误信息
     * 具体对应关系为：

     * 错误号	描述
     * 0	请求成功
     * -1	系统错误
     * 40009	key 长度大于 128 个字节
     * 40010	key 和 value 的长度和大于 1024 个字节
     * 40011	排行榜 key 对应的 value 值格式不对，具体见 warning
     * 60001	单用户存储 kv 超过 128 对
     * 其它	参数错误
     * 示例
     * body 示例如下:

     * {"kv_list":[{"key":"test", "value":"{\"ttgame\":{\"score\":1}}"}]}

     * 注意

     * 当 key 是开发者所配置的排行榜 key 时，value 的内容应该满足KVData所指出的形式， 即形如 "{\"ttgame\":{\"score\":1}}"
     */
    public function setUserStorage(string $openid, string $sessionKey, array $kv_list, $sig_method = 'hmac_sha256')
    {
        $params = array();
        $params['kv_list'] = $kv_list;

        $queryParams = array();
        $queryParams['openid'] = $openid;
        $queryParams['signature'] = \Bytedance\Helpers::signature4SessionKey(\json_encode($params), $sessionKey, $sig_method);
        $queryParams['sig_method'] = $sig_method;

        $rst = $this->_request->post($this->_url . 'set_user_storage', $params, $queryParams);
        return $this->_client->rst($rst);
    }

    /**
     * removeUserStorage
     * 删除上报到字节跳动的云存储服务的 key-value 数据。

     * 接口地址
     * POST https://developer.toutiao.com/api/apps/remove_user_storage
     * 输入
     * 名称	描述
     * access_token	服务端 API 调用标识，获取方法
     * openid	登录用户唯一标识
     * signature	用户登录态签名，参考用户登录态签名算法
     * sig_method	用户登录态签名的编码方法，参考用户登录态签名算法
     * key	(body 中) 要删除的用户数据的 key list
     * 输出
     * 返回值为 JSON 形式。

     * 当服务器端正确返回时，会返回如下信息：

     * 名称	数据类型	取值
     * errcode	number	0
     * 当服务器端无法正确返回时，会返回如下信息：

     * 名称	数据类型	描述
     * errcode	number	错误号
     * errmsg	string	错误信息
     * 具体对应关系为：

     * 错误号	描述
     * 0	请求成功
     * -1	系统错误
     * 其它	参数错误
     * 示例
     * body 示例如下:

     * {"key":["test"]}
     */
    public function removeUserStorage(string $openid, string $sessionKey, array $keys, $sig_method = 'hmac_sha256')
    {
        $params = array();
        $params['key'] = $keys;

        $queryParams = array();
        $queryParams['openid'] = $openid;
        $queryParams['signature'] = \Bytedance\Helpers::signature4SessionKey(\json_encode($params), $sessionKey, $sig_method);
        $queryParams['sig_method'] = $sig_method;

        $rst = $this->_request->post($this->_url . 'remove_user_storage', $params, $queryParams);
        return $this->_client->rst($rst);
    }
}
