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
     * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/content-security/content-security-detect
     * 内容安全检测
     * 检测一段文本是否包含违法违规内容。
     *
     * 请求地址
     * POST https://developer.toutiao.com/api/v2/tags/text/antidirt
     * 请求参数
     * 请求 Headers
     * 属性 数据类型 必填 说明
     * X-Token string 是 小程序 access_token，参考登录凭证检验
     * 请求 Body
     * 属性 数据类型 必填 说明
     * tasks array 是 检测任务列表
     * content string 是 检测的文本内容
     * 返回值
     * 正确返回的 JSON 如下：
     *
     * 属性 数据类型 说明
     * log_id string 请求 id
     * data string 检测结果列表
     * code number 检测结果-状态码
     * msg string 检测结果-消息
     * data_id string 检测结果-数据 id
     * task_id string 检测结果-任务 id
     * predicts array 检测结果-置信度列表
     * target string 检测结果-置信度-服务/目标
     * model_name string 检测结果-置信度-模型/标签
     * prob number 检测结果-置信度-概率，仅供参考，可以忽略
     * hit boolean 检测结果-置信度-结果，当值为 true 时表示检测的文本包含违法违规内容
     * errCode
     * 当请求失败时，会返回非 0 的 code，错误信息会携带在 message 中，错误码含义如下：
     *
     * 错误号 描述
     * 0 成功
     * 400 参数有误
     * 401 access_token 校验失败
     * 请求示例
     * 请求 body
     *
     * {
     * "tasks": [
     * {
     * "content": "要检测的文本"
     * }
     * ]
     * }
     *
     * 返回示例
     * 正常返回
     *
     * {
     * "log_id": "202008181611370100150421452708466F",
     * "data": [
     * {
     * "msg": "",
     * "code": 0,
     * "task_id": "MICROAPP_6862233737027911687",
     * "predicts": [
     * {
     * "prob": 0,
     * "hit": false,
     * "target": null,
     * "model_name": "short_content_antidirt"
     * }
     * ],
     * "data_id": null
     * }
     * ]
     * }
     *
     * 错误返回
     *
     * 当 access_token 检验失败时会返回如下信息：
     *
     * {
     * "error_id": "7bf3b7e299e9448796aa99b44750df68",
     * "code": 401,
     * "message": "[app token sign fail] bad token",
     * "exception": "[app token sign fail] bad token"
     * }
     *
     * Bug & Tip
     * 请求 body 的Content-Type限定为application/json。
     * 该接口请在开发者服务器端请求。
     */
    public function textAntidirt($text)
    {
        $tasks = [];
        $text = is_array($text) ? $text : (array) $text;

        foreach ($text as $content) {
            $tasks[] = [
                'content' => $content
            ];
        }
        $params = array();
        $params['tasks'] = $tasks;

        // X-Token 小程序 access_token，参考登录凭证检验
        $headers = array();
        $headers['X-Token'] = $this->_client->getAccessToken();
        $options = array();
        $options['headers'] = $headers;
        $rst = $this->_request->post($this->_url . 'text/antidirt', $params, array(), $options);
        return $this->_client->rst($rst);
    }

    /**
     * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/content-security/picture-detect-v2
     * 图片检测 V2
     * 检测图片是否包含违法违规内容。
     *
     * 请求地址
     * POST https://developer.toutiao.com/api/apps/censor/image
     * 请求参数
     * 请求 Body
     * 属性 数据类型 必填 说明
     * app_id string 是 小程序 ID
     * access_token string 是 小程序 access_token，参考登录凭证检验
     * image string 否 检测的图片链接
     * image_data string 否 图片数据的 base64 格式，有 image 字段时，此字段无效
     * 返回值
     * 正确返回的 JSON 如下：
     *
     * 属性 数据类型 说明
     * error number 检测结果-状态码
     * message string 检测结果-消息
     * predicts array 检测结果-置信度列表
     * model_name string 检测结果-置信度-模型/标签
     * hit boolean 检测结果-置信度-结果，当值为 true 时表示检测的图片包含违法违规内容，比如是广告
     * errCode
     * 当请求失败时，会返回非 0 的 error，错误信息会携带在 message 中，错误码含义如下：
     *
     * 错误号 描述
     * 0 成功
     * 1 参数有误
     * 2 access_token 校验失败
     * 3 图片下载失败
     * 4 服务内部错误
     * 图片检测返回模型特征
     * 模型特征 说明
     * porn 图片涉黄
     * cartoon_leader 领导人漫画
     * anniversary_flag 特殊标志
     * sensitive_flag 敏感旗帜
     * sensitive_text 敏感文字
     * leader_recognition 敏感人物
     * bloody 图片血腥
     * fandongtaibiao 未准入台标
     * plant_ppx 图片涉毒
     * high_risk_social_event 社会事件
     * high_risk_boom 爆炸
     * high_risk_money 人民币
     * high_risk_terrorist_uniform 极端服饰
     * high_risk_sensitive_map 敏感地图
     * great_hall 大会堂
     * cartoon_porn 色情动漫
     * party_founding_memorial 建党纪念
     * 请求示例
     * 请求 body
     *
     * {
     * "app_id": "ttxxxxxxxxxxxxxxxx",
     * "access_token": "0d495e15563015e3f599c742384f546cac4ce63911464106af8094a0581bae7386dcff77b1b9b6fc4c16b69c9048ba2a2846c7ae8d8f07aa8b84a52bcb4d560a5b8724d99f8816600b5xxxxxxxxxx",
     * "image": "https://image.url"
     * }
     *
     * 返回示例
     * 正常返回
     *
     * {
     * "error": 0,
     * "message": "image censor success",
     * "predicts": [
     * {
     * "model_name": "anniversary_flag",
     * "hit": false
     * },
     * {
     * "model_name": "bloody",
     * "hit": false
     * },
     * {
     * "model_name": "high_risk_boom",
     * "hit": false
     * },
     * {
     * "model_name": "cartoon_leader",
     * "hit": false
     * },
     * {
     * "model_name": "fandongtaibiao",
     * "hit": false
     * },
     * {
     * "model_name": "leader_recognition",
     * "hit": false
     * },
     * {
     * "model_name": "high_risk_money",
     * "hit": false
     * },
     * {
     * "model_name": "plant_ppx",
     * "hit": false
     * },
     * {
     * "model_name": "porn",
     * "hit": false
     * },
     * {
     * "model_name": "sensitive_flag",
     * "hit": false
     * },
     * {
     * "model_name": "high_risk_sensitive_map",
     * "hit": false
     * },
     * {
     * "model_name": "sensitive_text",
     * "hit": false
     * },
     * {
     * "model_name": "high_risk_social_event",
     * "hit": false
     * },
     * {
     * "model_name": "high_risk_terrorist_uniform",
     * "hit": false
     * },
     * {
     * "model_name": "party_founding_memorial",
     * "hit": false
     * },
     * {
     * "model_name": "cartoon_porn",
     * "hit": false
     * },
     * {
     * "model_name": "great_hall",
     * "hit": false
     * }
     * ]
     * }
     *
     * 错误返回
     *
     * 当 access_token 检验失败时会返回如下信息：
     *
     * {
     * "error": 2,
     * "message": "bad access_token"
     * }
     *
     * Bug & Tip
     * image 和 image_data 至少存在一个，同时存在时 image_data 无效。
     * 请求 body 的Content-Type限定为application/json。
     * 该接口请在开发者服务器端请求。
     * 常见问题
     * 已经添加图片检测，但审核打回：图片类型检测没有通过
     */
    public function censorImage($image, $image_data)
    {
        if (empty($image) && empty($image_data)) {
            throw new \Exception("image和image_data至少存在一个，同时存在时 image_data 无效。");
        }

        $params = array();
        $params['app_id'] = $this->_client->getAppid();
        $params['access_token'] = $this->_client->getAccessToken();
        $params['image'] = $image;
        $params['image_data'] = $image_data;
        $rst = $this->_request->post('https://developer.toutiao.com/api/apps/censor/image', $params, array());
        return $this->_client->rst($rst);
    }
}
