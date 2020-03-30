<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;

/**
 * 小程序/小游戏的二维码接口
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Qrcode
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/apps/';

    private $_client;

    private $_request;

    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }

    /**
     * createQRCode
     * 获取小程序/小游戏的二维码。该二维码可通过任意 app 扫码打开，能跳转到开发者指定的对应字节系 app 内拉起小程序/小游戏， 并传入开发者指定的参数。通过该接口生成的二维码，永久有效，暂无数量限制。

     * 接口地址
     * POST https://developer.toutiao.com/api/apps/qrcode
     * 输入
     * 以下参数均在 JSON body 中。

     * 名称	必填	默认值	描述
     * access_token	是		服务端 API 调用标识，获取方法
     * appname	否	toutiao	是打开二维码的字节系 app 名称，默认为今日头条，取值如下表所示
     * path	否		小程序/小游戏启动参数，小程序则格式为 encode({path}?{query})，小游戏则格式为 JSON 字符串，默认为空
     * width	否	430	二维码宽度，单位 px，最小 280px，最大 1280px，默认为 430px
     * line_color	否	{"r":0,"g":0,"b":0}	二维码线条颜色，默认为黑色
     * background	否		二维码背景颜色，默认为透明
     * set_icon	否	FALSE	是否展示小程序/小游戏 icon，默认不展示
     * appname的取值：

     * appname	对应字节系 app
     * toutiao	今日头条
     * douyin	抖音
     * pipixia	皮皮虾
     * huoshan	火山小视频
     * 注意

     * 在使用该功能之前请记得先配置您的默认分享文案和图片，配置方式可参考论坛。

     * 输出
     * 当服务器端正确返回时，返回图片 Buffer。

     * 当服务器端无法正确返回时，返回值为 JSON 形式，会返回如下信息：

     * 名称	数据类型	描述
     * errcode	number	错误号
     * errmsg	string	错误信息
     * 具体对应关系为：

     * 错误号	描述
     * 0	请求成功
     * -1	系统错误
     * 40002	access_token 错误
     * 40016	appname 错误
     * 40021	width 超过指定范围
     * 60003	频率限制（目前 5000 次/分钟）
     * 其它	其它参数错误
     * 示例
     * body 示例如下:

     * {
     * 	"appname": "toutiao",
     * 	"access_token": "XXX",
     * 	"path": "",
     * 	"width": 430,
     * 	"line_color": {"r": 0, "g": 0, "b": 0},
     * 	"background": {"r": 255, "g": 255, "b": 255},
     * 	"set_icon": true
     * }

     * 注意

     * 小程序的 path 要 encode 一次，如 pages%3fparam%3dtrue，小游戏的 path 为 JSON 字符串，如{"param":true}，否则会导致取不到。
     *        
     */
    public function create($appname = "toutiao", $path = "", $width = 430, $line_color = null, $background = null, $set_icon = false)
    {
        $appnameList = array(
            'toutiao', //今日头条
            'douyin', //抖音
            'pipixia', //皮皮虾
            'huoshan', //火山小视频
        );
        if (!in_array($appname, $appnameList)) {
            throw new \Exception("app名称:{$appname}不存在");
        }

        $params = array();
        $params['path'] = \urlencode($path);
        $params['access_token'] = $this->_client->getAccessToken();
        $params['width'] = $width;
        if (!empty($line_color)) {
            $params['line_color'] = $line_color;
        }
        if (!empty($background)) {
            $params['background'] = $background;
        }
        $params['set_icon'] = (empty($set_icon) ? false : true);

        $this->_request->setJson(false);
        $rst = $this->_request->post($this->_url . 'qrcode', $params);
        $rst = $this->getBody($rst);
        return $this->_client->rst($rst);
    }

    private function getBody($body)
    {
        $ret = array(
            'errcode' => 0,
            'errmsg' => '',
            'qrcode' => ''
        );

        // 如果为空值就是错误
        if (empty($body)) {
            $ret['errcode'] = 99999;
            $ret['errmsg'] = "生成失败";
            return $ret;
        }
        $result = json_decode($body, true);
        if (empty($result)) {
            $ret['qrcode'] = $body;
            return $ret;
        } else {
            return $result;
        }
    }
}
