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
   * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/data-caching/set-user-storage
   * setUserStorage更新时间：2022-10-13 17:51:27
   *
   * 以 key-value 形式存储用户数据到小程序平台的云存储服务。若开发者无内部存储服务则可接入，免费且无需申请。一般情况下只存储用户的基本信息，禁止写入大量不相干信息。
   *
   *
   * Bug&Tip
   * Tip：当 key 是开发者所配置的排行榜 key 时，value 的内容应该满足KVData所指出的形式。
   * Tip：该方法为服务端方法，实际方法效果与前端接口 tt.setUserCloudStorage 一致。通过该接口设置数据后，可以在前端通过 tt.getUserCloudStorage 或 tt.getCloudStorageByRelation 获取。
   */
  public function setUserStorage(string $openid, string $session_key, array $kv_list)
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
   * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/data-caching/remove-user-storage
   * removeUserStorage更新时间：2022-10-13 17:51:46
   *
   * 删除存储到字节跳动的云存储服务的 key-value 数据。当开发者不需要该用户信息时，需要删除，以免占用过大的存储空间。
   *
   * Bug&Tip
   * Tip: 该接口为服务端接口，接口的实际效果与前端 tt.removeUserCloudStorage 一致。
   */
  public function removeUserStorage(string $openid, string $session_key, array $key)
  {
    $sig_method = 'hmac_sha256';
    $params = array();
    $params['key'] = $key;

    $queryParams = array();
    $queryParams['openid'] = $openid;
    $queryParams['signature'] = \Bytedance\Helpers::signature4SessionKey(\json_encode($params), $session_key, $sig_method);
    $queryParams['sig_method'] = $sig_method;

    $rst = $this->_request->post($this->_url . 'remove_user_storage', $params, $queryParams);
    return $this->_client->rst($rst);
  }
}
