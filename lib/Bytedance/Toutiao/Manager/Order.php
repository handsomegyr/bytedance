<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 订单接口
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Order
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/apps/order/v2/';
    private $_client;
    private $_request;
    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/order/push
     * 小程序订单同步更新时间：2021-11-08 22:22:23
     * 简介
     * 开发者可通过订单同步接口将订单信息推送到抖音订单中心，便于用户查找订单信息，并提供回访小程序的入口。
     *
     *
     * 订单类型
     * 平台对接入的小程序提供了如下图所示的通用订单模板，可以满足小程序的日常订单复访需求。对于其他具体业务，例如生活服务，需要使用其他类型的订单进行业务的闭环。
     *
     * 订单 订单类型 适用场景 order_type 枚举值
     * 小程序订单 普通订单 无特殊业务诉求的小程序接入 0
     * 景区门票服务 生活服务订单 本地行业-景区门票 9001
     * 预售券订单 生活服务订单 本地行业-预售券 200
     * 团购券订单 生活服务订单 本地行业-团购券 9101
     * 住宿订单 生活服务订单 本地行业-住宿 140
     * 接口定义
     * POST https://developer.toutiao.com/api/apps/order/v2/push
     *
     * 输入参数
     * // Content-type:application/json
     * {
     * "client_key": "", // string 类型， POI订单必传
     * "access_token": "", // string类型，必传字段，服务端 API 调用标识，
     * "ext_shop_id": "", // 外部商户ID
     * "app_name": "douyin", // 必传字段，做订单展示的字节系 app 名称，取值枚举： 抖音-douyin
     * "open_id": "", // 小程序open id
     * "update_time": 0, // 订单信息变更时间，13位毫秒级时间戳
     * "order_detail": "", // 订单细节，根据不同订单类型有不同的结构体
     * "order_type": 0, // 订单类型 0 - 小程序订单
     * // 订单产生场景
     * "scene": "",
     * "launch_from": "",
     * "location": "",
     * "extra": ""
     * }
     *
     * 名称 类型 必填 描述
     * client_key string POI 订单必传 第三方在开放平台申请的 ClientKey
     * access_token string 是 服务端 API 调用标识，字节小程序 getAccessToken (bytedance.com)
     * ext_shop_id string POI 订单必传 POI 外部商户 ID，购买店铺 id
     * app_name string 是 做订单展示的字节系 app 名称，取值枚举： 抖音-douyin
     * open_id string 是 小程序用户的 open id
     * order_detail string 是 根据不同订单类型有不同的结构体
     * order_type int 是 订单类型，取值：0 - 小程序订单
     * update_time int 是 订单信息变更时间，13 位毫秒级时间戳
     * extra string 否 自定义字段，用于关联具体业务场景下的特殊参数
     * scene string 否 订单生成时的场景值
     * location string 否
     * launch_from string 否
     * 输出
     * 返回值为 JSON 形式，返回信息如下：
     *
     * 名称 数据类型 描述
     * err_code number 错误号
     * err_msg string 错误信息
     * body string 生活服务关联业务推送结果与业务信息，json 字符串。小程序订单可忽略
     * 具体对应关系为：
     *
     * 错误号 描述
     * 0 请求成功
     * -1 系统错误
     * 40002 access_token 错误
     * 40003 openid 错误
     * 40014 http 参数错误
     * 40016 appname 错误
     * 40022 business_line 错误
     * 接入流程
     * 该接口的普通小程序订单接入流程中，有白名单逻辑，需要先将测试用的 uid 加白，然后录屏展示订单全流程给平台确认后开全量，测试用户 uid 获取方式：抖音 app->我 tab->右上角更多->设置页面下拉至底部->version 点三下，uid 加白请联系 邹露雨。白名单测试阶段，只有 uid 加白的用户可以在订单中心中看到订单。在功能开发完成后，提供录屏经验收无误，可以开启全量订单显示。
     *
     * Q&A
     * 同步订单并且返回了成功，但是却没有在订单中心中看到订单：请检查 uid 是否开白。确认开白后，核实开白的过期时间，是否白名单配置超过了有效期。
     * 为什么我没有找到小程序订单的枚举: 普通小程序订单
     * 为什么我的订单只能在全部 Tab 下看到: 目前小程序订单还不支持 Tab 归类，只会在全部订单 Tab 下可见
     * 为什么我推送了订单，自己却看不到：IDE 中的 open id 生成逻辑与真机调试不同，如果使用 IDE 中的 open id，则会导致订单推送给错误的用户
     * 关于“抖音开放平台”和“小程序开发者平台”: 本次对接，需要申请独立的两套开发者账号；“抖音开放平台”用于店铺、SPU 和订单数据同步，“字节跳动小程序开发者平台”用于小程序开发
     * 传入参数错误可能会直接返回 HTTPcode 400，出现此情况请首先参照实例改变参数或者联系相关同学排查。
     * 订单详情 order_detail 格式定义
     * 小程序订单 detail 格式规范
     *
     * 以下参数均在 JSON body 中。
     *
     *
     * 名称 必填 数据类型 描述
     * order_id 是 string 订单 id
     * create_time 是 number 订单创建的时间，UNIX 时间戳
     * status 是 string 订单状态
     * amount 否 number 订单商品总数，number 类型
     * total_price 是 number 订单总价，必须为 Number 类型，单位为分
     * detail_url 是 string 小程序订单详情页 path
     * detail_url_backend 否 string 订单详情页备用 url，订单详情页 path 不可用时生效
     * item_list 是 list 子订单商品列表，不可为空
     * item_list 字段
     *
     * 名称 必填 数据类型 描述
     * item_code 否 string 子订单 id
     * img 是 string 子订单商品图片 url，建议尺寸 800*800，大小 500Kb
     * title 是 string 子订单商品介绍标题
     * sub_title 否 string 子订单商品介绍副标题
     * amount 否 number 单类商品的数目，必须为 number 类型
     * price 是 number 单类商品的总价,必须为 Number 类型，单位为分
     * 小程序订单中，对已存在的订单进行重复 push。会以 AppID 与 OrderID 进行判断。对非空值进行更新。
     *
     * 其他类型
     * 景区门票、团购券、预售券、住宿类型订单请联系相关人员一同接入。
     */
    public function push($client_key, $ext_shop_id, $app_name, $open_id, $order_detail, $order_type, $update_time, $extra = "", $scene = "", $location = "", $launch_from = "")
    {
        $params = array();
        $params['client_key'] = $client_key;
        $params['access_token'] = $this->_client->getAccessToken();
        $params['ext_shop_id'] = $ext_shop_id;
        $params['app_name'] = $app_name;
        $params['open_id'] = $open_id;
        $params['order_detail'] = $order_detail;
        $params['order_type'] = $order_type;
        $params['update_time'] = $update_time;
        $params['extra'] = $extra;
        $params['scene'] = $scene;
        $params['location'] = $location;
        $params['launch_from'] = $launch_from;

        $rst = $this->_request->post($this->_url . 'push', $params);
        return $this->_client->rst($rst);
    }
}
