<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 小程序的 url link
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Urllink
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/apps/url_link/';
    private $_client;
    private $_request;
    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/share/url-link-generate
     * Generate更新时间：2022-11-02 16:55:49
     *
     * 该接口用于生成能够直接跳转到端内小程序的 url link。
     */
    public function generate($ma_app_id, $app_name, $path = "pages/index", $query = "{}", $expire_time = 1665158399)
    {
        $appnameList = array(
            'douyin', // 抖音
            'douyinlite'
        ) // 抖音极速版
        ;
        if (!in_array($app_name, $appnameList)) {
            throw new \Exception("app名称:{$app_name}不存在");
        }

        $params = array();
        $params['access_token'] = $this->_client->getAccessToken();
        $params['ma_app_id'] = $ma_app_id;
        $params['app_name'] = $app_name;
        $params['path'] = $path;
        $params['query'] = $query;
        $params['expire_time'] = $expire_time;

        $rst = $this->_request->post($this->_url . 'generate', $params);
        return $this->_client->rst($rst);
    }

    /**
     * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/share/url-link-query
     * QueryInfo
     *
     * 该接口用于查询已经生成的 link 的信息。
     */
    public function queryInfo($ma_app_id, $url_link)
    {
        $params = array();
        $params['access_token'] = $this->_client->getAccessToken();
        $params['ma_app_id'] = $ma_app_id;
        $params['url_link'] = $url_link;

        $rst = $this->_request->post($this->_url . 'query_info', $params);
        return $this->_client->rst($rst);
    }

    /**
     * https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/develop/server/share/url-link-query-quota
     * QueryQuota
     *
     * 该接口用于查询当前小程序配额。
     */
    public function queryQuota($ma_app_id)
    {
        $params = array();
        $params['access_token'] = $this->_client->getAccessToken();
        $params['ma_app_id'] = $ma_app_id;

        $rst = $this->_request->post($this->_url . 'query_quota', $params);
        return $this->_client->rst($rst);
    }
}
