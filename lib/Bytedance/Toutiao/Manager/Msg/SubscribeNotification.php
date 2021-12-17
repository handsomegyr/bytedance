<?php

namespace Bytedance\Toutiao\Manager\Msg;

use Bytedance\Toutiao\Client;

/**
 * 订阅消息接口
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class SubscribeNotification
{
  // 接口地址
  private $_url = 'https://developer.toutiao.com/api/apps/subscribe_notification/developer/v1/';
  private $_client;
  private $_request;
  public function __construct(Client $client)
  {
    $this->_client = $client;
    $this->_request = $client->getRequest();
  }

  /**
   * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/subscribe-notification/notify
   * 订阅消息推送
   * 用户产生了订阅模板消息的行为后，可以通过这个接口发送模板消息给用户，功能参考订阅消息能力。
   *
   * 请求地址
   * POST https://developer.toutiao.com/api/apps/subscribe_notification/developer/v1/notify
   *
   * 请求参数
   * 请求 Body
   * 属性 数据类型 必填 说明
   * access_token string 是 小程序 access_token，参考登录凭证检验
   * app_id string 是 小程序的 id
   * tpl_id string 是 模板的 id，参考订阅消息能力
   * open_id string 是 接收消息目标用户的 open_id，参考code2session
   * data object 是 模板内容，格式形如 { "key1": "value1", "key2": "value2" }，具体使用方式参考下文请求示例
   * page string 否 跳转的页面
   * 返回值
   * 正确返回的 JSON 如下：
   *
   * 属性 数据类型 说明
   * err_no number 错误码
   * err_tips string 错误信息
   * errCode
   * 当请求失败时，会返回非 0 的 err_no，错误信息会携带在 err_tips 中，错误码含义如下：
   *
   * 错误号 描述
   * 0 成功
   * 1000 参数格式有误
   * 1001 参数内容有误
   * 1008 通知内容违规
   * 1009 推送消息能力被封禁
   * 1010 发送消息过于频繁
   * 2000 服务内部错误
   * 请求示例
   * 请求 body
   *
   * {
   * "access_token": "b13b4c7679150245dac2249aafd8aca8e9dceaff9e22cee05e1d30fb67e18916358d73c235fcaab9007ec0976ee40d26ee56a43c32398b9d6680105e5535cd1ef40a803d790668581************",
   * "app_id": "31198cf00b********",
   * "tpl_id": "MSG38489d04608c5f0fdeb565fc5114afff6410*******",
   * "open_id": "36d4bd3c8****",
   * "data": {
   * "物品名称": "测试值0",
   * "购买金额": "测试值1"
   * },
   * "page": "pages/index?a=b"
   * }
   *
   * 模板内容data中 key/value 与模板元素的对应关系如下图，模板元素查询参考订阅消息能力：
   *
   *
   * 返回示例
   * 正常返回
   *
   * {
   * "err_no": 0,
   * "err_tips": ""
   * }
   *
   * 错误返回
   *
   * {
   * "err_no": 1001,
   * "err_tips": "app_id / open_id不合法"
   * }
   *
   * Bug & Tip
   * 请求 body 的Content-Type限定为application/json
   * 对单个用户推送消息，频率限制为 1 次/秒。
   * 订阅消息分为一次性订阅和长期订阅，详情参考订阅消息能力。
   */
  public function notify($open_id, $tpl_id, $page, array $data)
  {
    $params = array();
    $params['access_token'] = $this->_client->getAccessToken();
    $params['app_id'] = $this->_client->getAppid();
    $params['tpl_id'] = $tpl_id;
    $params['open_id'] = $open_id;
    $params['data'] = $data;
    $params['page'] = $page;

    $rst = $this->_request->post($this->_url . 'notify', $params);
    return $this->_client->rst($rst);
  }
}
