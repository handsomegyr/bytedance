<?php

namespace Bytedance\Toutiao\Manager\Msg;

use Bytedance\Toutiao\Client;

/**
 * 模版消息接口
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Template
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/apps/game/template/';

    private $_client;

    private $_request;

    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * send
     * 提示

     * 本接口在服务器端调用
     * 目前只有今日头条支持，抖音和 lite 接入中
     * 发送模版消息

     * 接口地址
     * POST https://developer.toutiao.com/api/apps/game/template/send
     * 输入
     * 以下参数均在 JSON body 中。

     * 名称	类型	必填	含义
     * access_token	String	是	服务端 API 调用标识，获取方法
     * touser	String	是	要发送给用户的 open id, open id 的获取请参考登录
     * template_id	String	是	在开发者平台配置消息模版后获得的模版 id
     * page	String	否	点击消息卡片之后打开的小程序页面地址，空则无跳转
     * form_id	String	是	可以通过<form />组件获得 form_id, 获取方法
     * data	dict<String, SubData>	是	模板中填充着的数据，key 必须是 keyword 为前缀
     * SubData
     * SubData 也是 dict，结构如下：

     * 名称	类型	是否必填
     * value	String	是
     * 输出
     * 接口响应时一个 JSON body。结构如下：

     * 名称	类型	含义
     * errcode	Number	错误码
     * errmsg	String	成功为"success", 错误为具体 message
     * errcode
     * code	含义
     * 0	成功
     * -1	发生系统内部错误
     * 40001	http 包体无法解析
     * 40002	access_token 无效
     * 40014	参数无效
     * 40037	错误的模版 id
     * 40038	小程序被禁止发送消息通知
     * 40039	form_id 不正确，或者过期
     * 40040	form_id 已经被使用
     * 40041	错误的页面地址
     * 示例
     * 请求：

     *      * POST /api/apps/game/template/send HTTP/1.1
     * Host: developer.toutiao.com
     * Content-Type: application/json

     * {"access_token": "YOUR_ACCESS_TOKEN", "app_id": "YOUR_APP_ID", "data": {"keyword1": {"value": "v1"}, "keyword2": {"value": "v2"}}, "page": "pages/index", "form_id": "YOUR_FORM_ID", "touser": "USER_OPEN_ID", "template_id": "YOUR_TPL_ID"}
     * 响应：

     * HTTP/1.1 200 OK
     * Content-Type: application/json; charset=utf-8

     * {"errcode":0,"errmsg":"success"}      
     */
    public function send($touser, $template_id, $page, $form_id, array $data)
    {
        $params = array();
        $params['touser'] = $touser;
        $params['access_token'] = $this->_client->getAccessToken();
        $params['template_id'] = $template_id;
        $params['page'] = $page;
        $params['form_id'] = $form_id;
        $params['data'] = $data;

        $rst = $this->_request->post($this->_url . 'send', $params);
        return $this->_client->rst($rst);
    }
}
