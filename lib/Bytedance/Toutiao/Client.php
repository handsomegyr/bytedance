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
use Bytedance\Toutiao\Manager\Qrcode;
use Bytedance\Toutiao\Manager\Msg;
use Bytedance\Toutiao\Manager\Tags;

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
     * @return \Bytedance\Toutiao\Manager\UserStorage
     */
    public function getUserStorageManager()
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
     * 标准化处理服务端API的返回结果
     */
    public function rst($rst)
    {
        return $rst;
    }

    public function __destruct()
    {
    }
}
