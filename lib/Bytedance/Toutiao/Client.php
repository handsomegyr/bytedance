<?php

/**
 * 服务端 API
 * 字节跳动小程序、小游戏给开发者提供了服务端使用的 HTTPS API 接口。
 * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/server-api-introduction
 * 
 * @author guoyongrong <handsomegyr@126.com>
 *
 */

namespace Bytedance\Toutiao;

use Bytedance\Toutiao\Manager\Storage;
use Bytedance\Toutiao\Manager\Order;
use Bytedance\Toutiao\Manager\Qrcode;
use Bytedance\Toutiao\Manager\Msg;
use Bytedance\Toutiao\Manager\Tags;
use Bytedance\Toutiao\Manager\ConvertVideoId;
use Bytedance\Toutiao\Manager\Ecpay;

class Client
{
    private $_app_id = null;
    private $_accessToken = null;
    private $_request = null;
    public function __construct($app_id = "", $access_token = "")
    {
        $this->_app_id = $app_id;
        $this->_accessToken = $access_token;
    }

    /**
     * 获取服务端的appid
     *
     * @throws Exception
     */
    public function getAppid()
    {
        if (empty($this->_app_id)) {
            throw new \Exception("请设定app_id");
        }
        return $this->_app_id;
    }

    /**
     * 设定服务端的appid
     *
     * @param string $app_id        	
     */
    public function setAppid($app_id)
    {
        $this->_app_id = $app_id;
        return $this;
    }

    /**
     * 获取服务端的accessToken
     *
     * @throws Exception
     */
    public function getAccessToken()
    {
        if (empty($this->_accessToken)) {
            throw new \Exception("请设定access_token");
        }
        return $this->_accessToken;
    }

    /**
     * 设定服务端的access token
     *
     * @param string $accessToken        	
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
        return $this;
    }

    /**
     * 初始化认证的http请求对象
     */
    private function initRequest()
    {
        $this->_request = new \Bytedance\Http\Request($this->getAccessToken());
    }

    /**
     * 获取请求对象
     *
     * @return \Bytedance\Http\Request
     */
    public function getRequest()
    {
        if (empty($this->_request)) {
            $this->initRequest();
        }
        return $this->_request;
    }

    /**
     * 获取数据缓存管理器
     *
     * @return \Bytedance\Toutiao\Manager\Storage
     */
    public function getStorageManager()
    {
        return new Storage($this);
    }

    /**
     * 获取消息管理器
     *
     * @return \Bytedance\Toutiao\Manager\Msg
     */
    public function getMsgManager()
    {
        return new Msg($this);
    }

    /**
     * 获取二维码管理器
     *
     * @return \Bytedance\Toutiao\Manager\Qrcode
     */
    public function getQrcodeManager()
    {
        return new Qrcode($this);
    }

    /**
     * 获取内容安全管理器
     *
     * @return \Bytedance\Toutiao\Manager\Tags
     */
    public function getTagsManager()
    {
        return new Tags($this);
    }

    /**
     * 获取订单管理器
     *
     * @return \Bytedance\Toutiao\Manager\Order
     */
    public function getOrderManager()
    {
        return new Order($this);
    }

    /**
     * 获取抖音开放平台与小程序视频打通能力管理器
     *
     * @return \Bytedance\Toutiao\Manager\ConvertVideoId
     */
    public function getConvertVideoIdManager()
    {
        return new ConvertVideoId($this);
    }

    /**
     * 获取担保支付管理器
     *
     * @return \Bytedance\Toutiao\Manager\Ecpay
     */
    public function getEcpayManager()
    {
        return new Ecpay($this);
    }

    /**
     * 拍抖音黑白名单管理能力
     * 提供拍抖音的黑白名单管理能力，开发者可以决定哪些用户拍抖音时可以挂载他们的小程序(白名单)，哪些用户拍抖音时不可以挂载他们的小程序(黑名单)。
     *
     * 小程序设置了白名单后，只有白名单内的用户才能够挂载小程序，其他用户均不可挂载；小程序设置了黑名单后，黑名单内的用户不可以挂载小程序，其他用户均可以进行挂载；如果小程序黑白名单都没有设置，那么默认所有用户都可以进行挂载小程序；如果小程序同时设置了黑白名单，以白名单为准，只有白名单内的用户才可以挂载小程序。
     *
     * 请求地址
     * POST https://developer.toutiao.com/api/apps/share_config
     *
     * 请求验证
     * 请求需要验证授权，详见验证授权
     *
     * 请求参数
     * 属性 数据类型 必填 说明
     * appid string 是 小程序的 appID
     * uniq_id string 是 用户抖音号
     * access_token string 是 授权验证
     * type int 是 操作类型，详情见下表
     * 操作类型 type 参数详情
     *
     * Type 说明
     * 1 黑名单增加用户
     * 2 白名单增加用户
     * 3 黑名单删除用户
     * 4 白名单删除用户
     * 返回值
     * 返回值为 JSON 形式
     *
     * 当服务端正确返回时，会返回如下信息：
     *
     * 属性 数据类型 说明
     * data string 成功时，返回”success“
     * err_no int 错误号，成功时返回 0
     * err_tips string 错误信息，成功时返回空
     * errCode
     * 当请求失败时，会返回非 0 的err_no，错误信息会携带在 err_tips 中，错误码含义如下：
     *
     * 错误号 描述
     * -1 系统错误
     * 10001 传入 appid 为空
     * 10010 传入抖音号为空
     * 10011 传入 accessToken 为空
     * 11016 accessToken 校验不通过
     * 11017 无效的 uniqID
     * 11018 无效的操作行为类型
     * 请求示例
     * {
     * "appid": "tt5daf2b12c28****",
     * "uniq_id": "20107****",
     * "access_token": "08011218466f624b33532f75514364726168334243325a7********",
     * "type": 1
     * }
     *
     * 返回示例
     * 成功返回
     *
     * {
     * "data": "success",
     * "err_no": 0,
     * "err_tips": ""
     * }
     *
     * 错误返回
     *
     * {
     * "err_no": -1,
     * "err_tips": "system error"
     * }
     */
    public function shareConfig($uniq_id, $type)
    {
        if (in_array($type, array(
            1,
            2,
            3,
            4
        ))) {
            throw new \Exception("type 无效。");
        }
        $params = array();
        $params['appid'] = $this->getAppid();
        $params['uniq_id'] = $uniq_id;
        $params['access_token'] = $this->getAccessToken();
        $params['type'] = $type;
        $headers = array();
        $rst = $this->_request->post('https://developer.toutiao.com/api/apps/share_config', $params, $headers);
        return $this->_client->rst($rst);
    }

    /**
     * 标准化处理服务端API的返回结果
     */
    public function rst($rst)
    {
        return $rst;
    }
}
