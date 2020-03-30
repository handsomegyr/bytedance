<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 内容安全
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Tags
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/v2/tags/';

    private $_client;

    private $_request;

    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * 内容安全检测
     * 检测一段文本是否包含违法违规内容

     * 接口地址
     * POST https://developer.toutiao.com/api/v2/tags/text/antidirt
     * 注意

     * 该接口请在开发者服务器端请求

     * 参数
     * 请求 Headers
     * 属性	说明
     * X-Token	小程序 access_token，参考登录凭证检验
     * 请求 Body
     * {
     *   "tasks": [
     *     {
     *       "content": "要检测的文本"
     *     }
     *   ]
     * }

     * 属性	说明
     * tasks	检测任务列表
     * content	检测的文本内容
     * 返回
     * 正确返回的 JSON 如下：

     * {
     *   "log_id": "2018112821375501001506902813132",
     *   "data": [
     *     {
     *       "code": 0,
     *       "task_id": "d0229abe-f312-11e8-966d-0242ac110009",
     *       "data_id": null,
     *       "cached": false,
     *       "predicts": [
     *         {
     *          "prob": 1,
     *           "model_name": "antidirt",
     *           "target": "default"
     *         }
     *       ],
     *       "msg": "ok"
     *     }
     *   ]
     * }

     * 属性	说明
     * log_id	请求 id
     * data	检测结果列表
     * code	检测结果-状态码
     * msg	检测结果- 消息
     * data_id	检测结果-数据 id
     * task_id	检测结果-任务 id
     * predicts	检测结果-置信度列表
     * target	检测结果-置信度-服务/目标
     * model_name	检测结果-置信度-模型/标签
     * prob	检测结果-置信度-概率，值为 0 或者 1，当值为 1 时表示检测的文本包含违法违规内容
     * 当 access_token 检验失败时会返回如下信息：

     * {
     *   "error_id": "dd60b2bef95f40d7a4ace0bf5130d2e7",
     *   "code": 401,
     *   "message": "Authentication credentials were not provided.",
     *   "exception": "Authentication credentials were not provided."
     * }
     */
    public function textAntidirt($text)
    {
        $tasks = [];
        $text = is_array($text) ? $text : (array) $text;

        foreach ($text as $content) {
            $tasks[] = ['content' => $content];
        }
        $params = array();
        $params['tasks'] = $tasks;

        //X-Token	小程序 access_token，参考登录凭证检验
        $headers = array();
        $headers['X-Token'] = $this->_client->getAccessToken();
        $rst = $this->_request->post($this->_url . 'text/antidirt', $params, $headers);
        return $this->_client->rst($rst);
    }

    /**
     * 图片检测
     * 检测图片是否包含违法违规内容

     * 接口地址
     * POST https://developer.toutiao.com/api/v2/tags/image/
     * 注意

     * 该接口请在开发者服务器端请求

     * 参数
     * 请求 Headers
     * 属性	说明
     * X-Token	小程序 access_token，参考登录凭证检验
     * 请求 Body
     * {
     *   "targets": ["ad", "porn"],
     *   "tasks": [
     *     {
     *       "image": "https://image.url"
     *     }
     *   ]
     * }

     * 属性	说明
     * targets	图片检测服务类型，目前支持 porn、politics、ad、disgusting 四种
     * tasks	检测任务列表
     * image	检测的图片链接
     * image_data	图片数据的 base64 格式，有 image 字段时，此字段无效
     * 返回
     * 正确返回的 JSON 如下：

     * {
     *   "log_id": "2019010320551501001001621510071",
     *   "data": [
     *     {
     *       "code": 0,
     *       "task_id": "d18197c4-0f56-11e9-99a5-0242ac110004",
     *       "data_id": null,
     *       "cached": false,
     *       "predicts": [
     *         {
     *           "prob": 1,
     *           "model_name": "image_ocr",
     *           "target": "ad"
     *         },
     *         {
     *           "prob": 0,
     *           "model_name": "image_qrcode",
     *           "target": "ad"
     *         }
     *       ],
     *       "msg": "ok"
     *     },
     *     {
     *       "code": 0,
     *       "task_id": "d1aedc02-0f56-11e9-99a5-0242ac110004",
     *       "data_id": null,
     *       "cached": false,
     *       "predicts": [
     *         {
     *           "prob": 0.0005013857153244317,
     *           "model_name": "image_porn",
     *           "target": "porn"
     *         },
     *         {
     *           "prob": 0.022131478413939476,
     *           "model_name": "image_vulgar",
     *           "target": "porn"
     *         }
     *       ],
     *       "msg": "ok"
     *     }
     *   ]
     * }

     * 属性	说明
     * log_id	请求 id
     * data	检测结果列表
     * code	检测结果-状态码
     * msg	检测结果-消息
     * data_id	检测结果-数据 id
     * task_id	检测结果-任务 id
     * predicts	检测结果-置信度列表
     * target	检测结果-置信度-服务/目标
     * model_name	检测结果-置信度-模型/标签
     * prob	检测结果-置信度-概率，值为 0 或者 1，当值为 1 时表示检测的图片有违法违规内容比如是广告
     * 当 access_token 检验失败时会返回如下信息：

     * {
     *   "error_id": "dd60b2bef95f40d7a4ace0bf5130d2e7",
     *   "code": 401,
     *   "message": "Authentication credentials were not provided.",
     *   "exception": "Authentication credentials were not provided."
     * }
     */
    public function imageAntidirt($images)
    {
        $tasks = [];
        $images = is_array($images) ? $images : (array) $images;

        foreach ($images as $image) {
            $tasks[] = ['image' => $image];
        }

        if (empty($targets)) {
            $targets = [
                'porn', 'politics', 'ad', 'disgusting'
            ];
        }

        $params = [
            'targets' => $targets, 'tasks' => $tasks
        ];

        //X-Token	小程序 access_token，参考登录凭证检验
        $headers = array();
        $headers['X-Token'] = $this->_client->getAccessToken();
        $rst = $this->_request->post($this->_url . 'image', $params, $headers);
        return $this->_client->rst($rst);
    }
}
