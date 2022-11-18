<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 小程序的客服
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Chat
{
  // 接口地址
  private $_url = 'https://developer.toutiao.com/api/apps/chat/';
  private $_client;
  private $_request;
  public function __construct(Client $client)
  {
    $this->_client = $client;
    $this->_request = $client->getRequest();
  }

  /**
   * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/share/url-link-generate
   * CustomerServiceUrl更新时间：2022-10-25 10:45:06
   * 该接口用于获取官方平台客服链接。
   */
  public function customerServiceUrl($appid, $openid, $type, $scene, $im_type, $order_id)
  {
    $params = array();
    $params['access_token'] = $this->_client->getAccessToken();
    $params['appid'] = $appid;
    $params['openid'] = $openid;
    $params['type'] = $type;
    $params['scene'] = $scene;
    $params['im_type'] = $im_type;
    $params['order_id'] = $order_id;

    $headers = array();
    $headers['Access-Token'] = $this->_client->getAccessToken();
    $options = array();
    $options['headers'] = $headers;
    $rst = $this->_request->get($this->_url . 'customer_service_url', $params, $options);
    return $this->_client->rst($rst);
  }
}
