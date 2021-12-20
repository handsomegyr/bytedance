<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 抖音开放平台与小程序视频打通能力
 * 提供将抖音和小程序平台的视频信息互换使用的能力。开发者可以通过将小程序平台的视频转换到抖音开放平台，从而提供更丰富的视频相关资源和数据获取能力，另一方面，也可以通过从开放平台转换获取存量视频进行内容聚合和存量内容使用，扩大小程序的视频使用能力。
 *
 * 另外，关于打通功能，推荐阅读：小程序获取抖音权限
 * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/other/video-id-convert
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class ConvertVideoId
{
  // 接口地址
  private $_url = 'https://developer.toutiao.com/api/apps/convert_video_id/';
  private $_client;
  private $_request;
  public function __construct(Client $client)
  {
    $this->_client = $client;
    $this->_request = $client->getRequest();
  }

  /**
   * 数据获取能力
   * 提供将在抖音及今日头条宿主小程序拍摄抖音能力获取的 videoId 转换为抖音开放平台可用的 item_id，从而提供获取更多数据的能力。
   * 需要注意的是：开放平台端获取数据也需要经过抖音开放平台的获取 access_token 流程，这部分请参阅抖音开放平台的文档。
   *
   * 与原本数据获取能力的对比
   * 数据类型 小程序接口 抖音开放平台
   * 点赞数 可以获得 可以获得
   * 视频封面 可以获得 可以获得
   * 分享链接 不能获得 可以获得
   * 转发数 不能获得 可以获得
   * 评论数 不能获得 可以获得
   * 下载数 不能获得 可以获得
   * 播放数 不能获得 可以获得
   * 请求地址
   * POST https://developer.toutiao.com/api/apps/convert_video_id/video_id_to_open_item_id
   *
   * 请求验证
   * 请求需要验证授权，详见验证授权
   *
   * 请求参数
   * 请求应为 JSON 形式（Content-Type 头为 application/json）。
   *
   * 属性 数据类型 必填 说明
   * video_ids list[string] 是 要转换的 videoId 列表，最长为 100 个
   * app_id string 是 小程序 ID
   * access_token string 是 授权验证
   * access_key string 是 访问密钥，详情见下
   * access_key 逻辑
   *
   * 根据打通文档的说明，此处在使用的应用类型为小程序时应当为小程序的 appid，更详细的内容请参见小程序获取抖音权限结尾部分。
   *
   * 返回值
   * 返回值为 JSON 形式
   *
   * 当服务端正确返回时，会返回如下信息：
   *
   * 属性 数据类型 说明
   * data object 见下文结构说明
   * err_no int 错误号，成功时返回 0
   * err_tips string 错误信息，成功时返回空
   * data 结构说明
   *
   * data 结构为如下 JSON 字典
   *
   * {
   * "convert_result": {
   * "videoid1": "result1",
   * "videoid2": "result2"
   * }
   * }
   *
   * 转换失败的 videoId 将会在结果中被静默忽略。
   *
   * 当完成拍视频得到 videoId 后如果立即进行转换，因为服务端有延迟，一般是会失败的，建议先将 videoId 存到服务端，稍后再根据业务需要进行转换。
   *
   * errCode
   * 当请求失败时，会返回非 0 的err_no，错误信息会携带在 err_tips 中，错误码含义如下：
   *
   * 错误号 描述
   * 11001 无效或与 accessToken 不符的 appID
   * 11009 传入的参数不合法
   * -1 服务器出错，请稍后重试或验证请求是否合法
   * 请求示例
   * {
   * "app_id": "tt5d***",
   * "video_ids": [
   * "131041******************",
   * "131042******************",
   * "131051******************"
   * ],
   * "access_key": "tt5d***********",
   * "access_token": "0801121846********"
   * }
   *
   * 返回示例
   * 成功返回
   *
   * {
   * "err_no": 0,
   * "err_tips": "",
   * "data": {
   * "convert_result": {
   * "13104******": "Qy**************"
   * }
   * }
   * }
   *
   * 错误返回
   *
   * {
   * "err_no": -1,
   * "err_tips": "VideoIdToOpenItemId 服务器内部错误"
   * }
   */
  public function videoIdToOpenItemId($video_ids, $access_key)
  {
    $params = array();
    $params['app_id'] = $this->_client->getAppid();
    $params['access_token'] = $this->_client->getAccessToken();
    $params['video_ids'] = $video_ids;
    $params['access_key'] = $access_key;
    $rst = $this->_request->post($this->_url . 'video_id_to_open_item_id', $params, array());
    return $this->_client->rst($rst);
  }

  /**
   * 视频使用能力能力
   * 提供将在抖音开放平台获取的 item_id 转换为小程序中可用的 encryptedId，从而在小程序宿主中播放。通过这一能力，可以将原先无法获取的用户存量视频在字节小程序中使用。参见跳转视频播放页。
   *
   * 请求地址
   * POST https://developer.toutiao.com/api/apps/convert_video_id/open_item_id_to_encrypt_id
   *
   * 请求参数
   * 请求应为 JSON 形式（Content-Type 头为 application/json）。
   *
   * 属性 数据类型 必填 说明
   * video_ids list[string] 是 要转换的 item_id 列表，最长为 100 个
   * access_key string 是 访问密钥，详情见下表
   * access_key 逻辑
   *
   * 根据打通文档的说明，此处在使用的应用类型为小程序时应当为小程序的 appid。具体内容参见上面的 access_key 说明
   *
   * 返回值
   * 返回值为 JSON 形式
   *
   * 当服务端正确返回时，会返回如下信息：
   *
   * 属性 数据类型 说明
   * data object 见下文结构说明
   * err_no int 错误号，成功时返回 0
   * err_tips string 错误信息，成功时返回空
   * data 结构说明
   *
   * data 结构为如下 JSON 字典
   *
   * {
   * "convert_result": {
   * "item_id1": "result1",
   * "item_id2": "result2"
   * }
   * }
   *
   * 转换失败的 item_id 将会在结果中被静默忽略。
   *
   * errCode
   * 当请求失败时，会返回非 0 的err_no，错误信息会携带在 err_tips 中，错误码含义如下：
   *
   * 错误号 描述
   * 11009 传入的参数不合法
   * -1 服务器出错，请稍后重试或验证请求是否合法
   * 请求示例
   * {
   * "video_ids": [
   * "@ty******************",
   * "@ty******************",
   * "@ty******************"
   * ],
   * "access_key": "tt*************"
   * }
   *
   * 返回示例
   * 成功返回
   *
   * {
   * "err_no": 0,
   * "err_tips": "",
   * "data": {
   * "convert_result": {
   * "@ty******************": "dm**************"
   * }
   * }
   * }
   *
   * 错误返回
   *
   * {
   * "err_no": -1,
   * "err_tips": "OpenItemIdToEncryptedId 服务器内部错误"
   * }
   */
  public function openItemIdToEncryptId(array $video_ids, $access_key)
  {
    $params = array();
    $params['video_ids'] = $video_ids;
    $params['access_key'] = $access_key;
    $rst = $this->_request->post($this->_url . 'open_item_id_to_encrypt_id', $params, array());
    return $this->_client->rst($rst);
  }
}
