<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 数据缓存
 * 
 * @author guoyongrong <handsomegyr@126.com>
 */
class Storage
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
   * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/data-caching/set-user-storage
   * setUserStorage
   * 以 key-value 形式存储用户数据到小程序平台的云存储服务。若开发者无内部存储服务则可接入，免费且无需申请。一般情况下只存储用户的基本信息，禁止写入大量不相干信息。
   *
   * 请求地址
   * POST https://developer.toutiao.com/api/apps/set_user_storage
   * 请求参数
   * 属性 数据类型 是否必填 说明
   * access_token string 是 服务端 API 调用标识，获取方法
   * openid string 是 登录用户唯一标识 获取方法
   * signature string 是 用户登录态签名，参考用户登录态签名算法
   * sig_method string 是 用户登录态签名的编码方法，参考用户登录态签名算法
   * kv_list Array< KvItem> 是 (body 中) 要设置的用户数据
   * KvItem 结构体
   *
   * 属性 数据类型 说明
   * key string 键
   * value string 值
   * 返回值
   * 返回值为 JSON 形式。
   *
   * 当服务器端正确返回时，会返回如下信息：
   *
   * 属性 数据类型 说明
   * error int64 错误号, 正确返回时为 0
   * errCode
   * 当服务器端无法正确返回时，会返回如下信息：
   *
   * 属性 数据类型 说明
   * error int64 返回非 0
   * errcode int64 详细错误号
   * errmsg string 错误信息
   * message string 错误信息（同 errmsg）
   * 具体对应关系为：
   *
   * 错误号 说明
   * 0 请求成功
   * -1 系统错误
   * 40009 key 长度大于 128 个字节
   * 40010 key 和 value 的长度和大于 1024 个字节
   * 40011 排行榜 key 对应的 value 值格式不对，具体见 warning
   * 60001 单用户存储 kv 超过 128 对
   * 其它 参数错误
   * 请求示例
   * {
   * "kv_list": [
   * {
   * "key": "test",
   * "value": "{\"ttgame\":{\"score\":1}}"
   * }
   * ]
   * }
   *
   * 其余参数传入 url
   *
   * 返回示例
   * 正确返回
   *
   * {
   * "error": 0
   * }
   *
   * 错误返回
   *
   * {
   * "errcode": 40002,
   * "errmsg": "nil access_token",
   * "error": 1,
   * "message": "nil access_token"
   * }
   *
   * Tip：当 key 是开发者所配置的排行榜 key 时，value 的内容应该满足KVData所指出的形式。
   */
  public function setUserStorage(string $openid, string $session_key, array $kv_list, $sig_method = 'hmac_sha256')
  {
    $sig_method = 'hmac_sha256';
    $params = array();
    $params['kv_list'] = $kv_list;

    $queryParams = array();
    $queryParams['openid'] = $openid;
    $queryParams['signature'] = \Bytedance\Helpers::signature4SessionKey(\json_encode($params), $session_key, $sig_method);
    $queryParams['sig_method'] = $sig_method;

    $rst = $this->_request->post($this->_url . 'set_user_storage', $params, $queryParams);
    return $this->_client->rst($rst);
  }

  /**
   * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/data-caching/remove-user-storage
   * removeUserStorage
   * 删除存储到字节跳动的云存储服务的 key-value 数据。当开发者不需要该用户信息时，需要删除，以免占用过大的存储空间。
   *
   * 请求地址
   * POST https://developer.toutiao.com/api/apps/remove_user_storage
   * 请求参数
   * 属性 数据类型 是否必填 说明
   * access_token string 是 服务端 API 调用标识，获取方法
   * openid string 是 登录用户唯一标识 获取方法
   * signature string 是 用户登录态签名，参考用户登录态签名算法
   * sig_method string 是 用户登录态签名的编码方法，参考用户登录态签名算法
   * key Array< string> 是 (body 中) 要删除的用户数据的 key list
   * 返回值
   * 当服务器端正确返回时，会返回如下信息：
   *
   * 属性 数据类型 说明
   * error int64 错误号, 正确返回时为 0
   * errCode
   * 当服务器端无法正确返回时，会返回如下信息：
   *
   * 属性 数据类型 说明
   * error int64 返回非 0
   * errcode int64 详细错误号
   * errmsg string 错误信息
   * message string 错误信息（同 errmsg）
   * 具体对应关系为：
   *
   * 错误号 描述
   * 0 请求成功
   * -1 系统错误
   * 其它 参数错误
   * 请求示例
   * {
   * "key": ["test", "temp"]
   * }
   *
   * 其他参数传在 url 里
   *
   * 返回示例
   * 正确返回
   *
   * {
   * "error": 0
   * }
   *
   * 错误返回
   *
   * {
   * "errcode": 40002,
   * "errmsg": "nil access_token",
   * "error": 1,
   * "message": "nil access_token"
   * }
   */
  public function removeUserStorage(string $openid, string $session_key, array $keys, $sig_method = 'hmac_sha256')
  {
    $params = array();
    $params['key'] = $keys;

    $queryParams = array();
    $queryParams['openid'] = $openid;
    $queryParams['signature'] = \Bytedance\Helpers::signature4SessionKey(\json_encode($params), $session_key, $sig_method);
    $queryParams['sig_method'] = $sig_method;

    $rst = $this->_request->post($this->_url . 'remove_user_storage', $params, $queryParams);
    return $this->_client->rst($rst);
  }
}
