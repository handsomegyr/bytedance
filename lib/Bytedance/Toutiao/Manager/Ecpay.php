<?php

namespace Bytedance\Toutiao\Manager;

use Bytedance\Helpers;
use Bytedance\Toutiao\Client;

/**
 * 担保支付接口
 * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/ecpay/introduction
 * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/ecpay/merchant
 * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/ecpay/server-doc
 *
 * @author guoyongrong <handsomegyr@126.com>
 */
class Ecpay
{
    // 接口地址
    private $_url = 'https://developer.toutiao.com/api/apps/';
    private $_client;
    private $_request;
    private $_payment_salt;
    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->_request = $client->getRequest();
    }
    public function getPaymentSalt()
    {
        if (empty($this->_payment_salt)) {
            throw new \Exception("salt is empty");
        }
        return $this->_payment_salt;
    }
    public function setPaymentSalt($payment_salt)
    {
        $this->_payment_salt = $payment_salt;
    }

    /**
     * 小程序担保支付接入文档-进件篇
     * 基本概念
     * 小程序担保支付中，需要为每一个参与交易的参与方进行一次进件。这里的参与交易包含但不限于以下的行为：
     *
     * 商户售卖商品，接收货款
     * 服务商代授权小程序调用担保支付接口，并收取服务费
     * 分账方参与分账，获得交易的分润
     * 根据以上规则，按小程序开发的不同模式，小程序开发者直接开发与委托第三方服务商开发两种，可以将进件划分为以下的 case：
     *
     * 小程序开发者为自己进件
     * 服务商为自己进件
     * 服务商为委托开发的小程序进件
     * 小程序开发者为第三方进件
     * 服务商为第三方进件
     * 其中小程序开发者为自己进件，可以在小程序开发者平台上的相关页面直接操作。对于其他场景，我们认为接入方有多方进件与管理平台搭建的诉求，通过接口提供进件页面 url 与对应商户号账户页面 url 的方式，协助开发者快速搭建平台。 在进件完成后，可以以商户号的维度，下载交易的流水进行对账。
     *
     * 服务商进件接口
     * 在服务商完成担保支付开发者授权后，该接口通过获取进件页 url 来实现服务商的自行进件。使用 component_access_token 调用接口。
     * 接口链接：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/saas/add_merchant
     *
     * 输入
     *
     * 名称 数据类型 是否必传 描述
     * component_access_token string 是 授权码兑换接口调用凭证， 参考第三方平台文档：
     * thirdparty_component_id string 是 小程序第三方平台应用 id
     * url_type number 是 链接类型：1-进件页面 2-账户余额页
     * 输出
     *
     * 名称 数据类型 描述
     * err_no number 错误码，成功时为 0
     * err_tips string 错误信息
     * url string 请求页面链接，两小时过期，注意定时更新，请勿长期保存。
     * merchant_id string 小程序平台分配商户号，用于后续分账标识分账方。进件完成后，才会有非空返回
     */
    public function addMerchant($component_access_token, $thirdparty_component_id, $url_type)
    {
        $params = array();
        $params['component_access_token'] = $component_access_token;
        $params['thirdparty_component_id'] = $thirdparty_component_id;
        $params['url_type'] = $url_type;
        $rst = $this->_request->post($this->_url . 'ecpay/saas/add_merchant', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 服务商代小程序进件
     * 接口用于服务商为授权的小程序提供进件及结算能力。使用第三方平台提供的服务商支付 secret 对请求进行加签。secret 可以在在第三方平台的设置->开发设置中查看。
     * 接口链接：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/saas/get_app_merchant
     *
     * 输入
     *
     * 名称 数据类型 是否必传 描述
     * thirdparty_id string 是 小程序第三方平台应用 id
     * app_id string 是 小程序的 app_id
     * url_type number 是 链接类型：1-进件页面 2-账户余额页
     * sign string 是 签名，参考附录签名算法
     * 输出
     *
     * 名称 数据类型 描述
     * err_no number 错误码，成功时为 0
     * err_tips string 错误信息
     * url string 请求页面链接，两小时过期，注意定时更新，请勿长期保存。
     * merchant_id string 小程序平台分配商户号，用于后续分账标识分账方。
     */
    public function getAppMerchant($thirdparty_id, $app_id, $url_type)
    {
        $params = array();
        $params['thirdparty_id'] = $thirdparty_id;
        $params['app_id'] = $app_id;
        $params['url_type'] = $url_type;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/saas/get_app_merchant', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 小程序为第三方进件
     * 接口用于小程序开发者为其他关联业务方提供进件及结算能力。使用前要求小程序开发者首先完成自身账户进件，使用开发者平台提供的支付 SALT 对请求进行加签。
     * 接口链接：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/saas/app_add_sub_merchant
     *
     * 输入
     *
     * 名称 数据类型 是否必传 描述
     * sub_merchant_id string 是 商户 id，用于接入方自行标识并管理进件方。由开发者自行分配管理
     * app_id string 是 小程序的 app_id
     * url_type number 是 链接类型：1-进件页面 2-账户余额页
     * sign string 是 签名，参考附录签名算法
     * 输出
     *
     * 名称 数据类型 描述
     * err_no number 错误码，成功时为 0
     * err_tips string 错误信息
     * url string 请求页面链接，两小时过期，注意定时更新，请勿长期保存。
     * merchant_id string 小程序平台分配商户号，用于后续分账标识分账方。
     * 注：这里的 sub_merchant_id 只是为了便于小程序开发者区分管理，在分账时用于标识接受方的商户号为 response 中的 merchant_id 字段。
     */
    public function appAddSubMerchant($sub_merchant_id, $app_id, $url_type)
    {
        $params = array();
        $params['sub_merchant_id'] = $sub_merchant_id;
        $params['app_id'] = $app_id;
        $params['url_type'] = $url_type;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/saas/app_add_sub_merchant', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 服务商为第三方进件
     * 接口用于服务商为其他关联业务方提供进件及结算能力。使用前要求服务商首先完成自身账户进件，使用第三方平台提供的服务商支付 SALT 对请求进行加签。SALT 可以在在第三方平台的设置->开发设置中查看。
     * 接口链接：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/saas/add_sub_merchant
     *
     * 输入
     *
     * 名称 数据类型 是否必传 描述
     * sub_merchant_id string 是 商户 id，用于接入方自行标识并管理进件方。由开发者自行分配管理
     * thirdparty_id string 是 小程序第三方平台应用 id
     * url_type number 是 链接类型：1-进件页面 2-账户余额页
     * sign string 是 签名，参考附录签名算法
     * 输出
     *
     * 名称 数据类型 描述
     * err_no number 错误码，成功时为 0
     * err_tips string 错误信息
     * url string 请求页面链接，两小时过期，注意定时更新，请勿长期保存。
     * merchant_id string 小程序平台分配商户号，用于后续分账标识分账方。
     * 注：这里的 sub_merchant_id 只是为了便于小程序开发者区分管理，在分账时用于标识接受方的商户号为 response 中的 merchant_id 字段。
     */
    public function addSubMerchant($sub_merchant_id, $thirdparty_id, $url_type)
    {
        $params = array();
        $params['sub_merchant_id'] = $sub_merchant_id;
        $params['thirdparty_id'] = $thirdparty_id;
        $params['url_type'] = $url_type;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/saas/add_sub_merchant', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 进件状态查询
     * 提供进件结果查询接口，开发者/服务商可以通过接口查询到分账方进件结果。请求需要使用开发者/第三方平台提供的支付 SALT 进行加签。具体使用方式请参请求 Demo。
     * 接口链接：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/saas/query_merchant_status
     *
     * 输入
     *
     * 名称 数据类型 描述
     * app_id string 小程序 AppID
     * thirdparty_id string 第三方平台服务商 id，非服务商模式留空
     * merchant_id string 小程序平台分配商户号
     * sub_merchant_id string 商户 id，用于接入方自行标识并管理进件方。由服务商自行分配管理
     * sign string 签名，参考附录签名算法
     * 返回值：
     *
     * 名称 数据类型 描述
     * err_no int 错误码：成功-0，商户号和 APPID 或三方 ID 不对应-4000，内部异常-1000，签名校验异常-2008
     * err_tips string 错误信息
     * wx int 微信渠道进件状态，0-未进件、1-进件成功、2-进件失败、3-审核中、4-冻结中
     * alipay int 支付宝渠道进件状态，0-未进件、1-进件成功、2-进件失败、3-审核中
     * 请求 Demo：
     *
     * 开发者查询分账方商户，参数需传入 app_id、sign、merchant_id 以及 thirdparty_id，具体实例如下：
     * curl --location --request POST 'https://developer.toutiao.com/api/apps/ecpay/saas/query_merchant_status' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "app_id": "ttdb96ad2b44aeff3301",
     * "sign": "c639b06f45e74c7b8025acbb45f04bb1",
     * "merchant_id": "69824283230789983130",
     * "sub_merchant_id": "",
     * "thirdparty_id": "ttc4a8b2155b82682f"
     * }'
     *
     * 返回值：
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "wx": 1,
     * "alipay": 0
     * }
     *
     * 服务商查询分账方商户，参数需传入 sign、sub_merchant_id 以及 thirdparty_id，具体实例如下：
     * curl --location --request POST 'https://developer.toutiao.com/api/apps/ecpay/saas/query_merchant_status' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "app_id": "",
     * "sign": "14784648abca76344b87135fe810247b",
     * "merchant_id": "",
     * "sub_merchant_id": "69560302266147330860",
     * "thirdparty_id": "ttd74ca1f148667a24"
     * }'
     *
     * 返回值：
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "wx": 1,
     * "alipay": 1
     * }
     *
     * 服务商查询授权小程序，参数需传入 app_id、sign、merchant_id 以及 thirdparty_id，具体实例如下：
     * curl --location --request POST 'https://developer.toutiao.com/api/apps/ecpay/saas/query_merchant_status' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "app_id": "ttdb96ad2b44aeff3301",
     * "sign": "c639b06f45e74c7b8025acbb45f04bb1",
     * "merchant_id": "69824283230789983130",
     * "sub_merchant_id": "",
     * "thirdparty_id": "ttc4a8b2155b82682f"
     * }'
     *
     * 返回值：
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "wx": 1,
     * "alipay": 0
     * }
     */
    public function queryMerchantStatus($app_id, $merchant_id, $sub_merchant_id, $thirdparty_id)
    {
        $params = array();
        $params['app_id'] = $app_id;
        $params['merchant_id'] = $merchant_id;
        $params['sub_merchant_id'] = $sub_merchant_id;
        $params['thirdparty_id'] = $thirdparty_id;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/saas/query_merchant_status', $params, array());
        return $this->_client->rst($rst);
    }

    /**
     * 小程序担保支付接入文档-服务端更新时间：2021-12-16 17:04:42
     * 接入准备
     * 进件
     * 普通开发者可以在开发者平台的相关页面自行进件。
     * 服务商需要进行担保支付开发的，需要调用 服务商进件接口 来完成服务商的进件。
     * 如果业务逻辑中涉及其他业务方需要承接交易，或者参与分润，可以使用接口为关联的业务方进件。 开发者为业务方进件可以参考： 开发者为业务方进件 服务商为三方进件参考：服务商为第三方进件
     * 接口加签/验签
     * 在研发进入对接阶段前需要完成接口加签验签逻辑的开发。
     *
     * 完成进件后，可以从平台中获得支付系统秘钥 SALT 对于开发者，可以在小程序开发者平台后台进行进件，并在担保支付设置页面内获得。 对于服务商，使用服务商进件接口 进行进件，随后可以在第三方平台中查看分配的秘钥
     * 在小程序开发者平台上的担保支付平台设置页面中，填写回调相关配置。回调 token 与回调 url。该回调地址为必填项。开发者可以在请求中动态指定回调地址，动态指定地址会覆盖默认配置。
     * 服务商在完成小程序的支付授权后，可以使用服务商秘钥供待开发小程序调用
     * 参考附录中的加签/验签逻辑完成请求的加签与回调通知的验签 请求加签参考逻辑与其中的 DEMO，并可通过小程序开发者工具对加签逻辑进行校验 开发者需要搭建服务，通过配置回调 Url 接收小程序平台推送的支付消息。验签逻辑可以参考 回调签名算法中的内容。要注意，回调验签使用的 token 为小程序开发者平台内担保交易设置中的内容，不同于其他任意 token。
     * 接口设计
     * 所有接口均使用小程序+开发者单号进行幂等性的判断。对应已经存在的单号，若首次请求失败，后续的所有调用都会进行错误返回，并提示开发者更换新的业务单号。
     * 对于同一业务单号的请求，会直接使用首次请求所有参数，并会忽略掉其他的参数变化。请避免同一单号携带不同参数请求的 case，否则可能会导致预期外的行为。
     * 业务提交接口返回单号仅表示业务提交成功，业务处理结果依赖异步的回调通知或查询。例如退款成功提交后，可能会受余额影响处理失败。
     * 接入摘要
     * 小程序接入担保支付需要对接支付，退款与分账三个功能。三个功能又对应各自的查询与回调。 以支付接口为例，一笔订单通过担保支付预下单接口后，进行支付收银台拉起，由用户完成支付后。会由小程序平台服务端向开发者指定的回调地址发送请求，通知业务处理结果。回调通知再出现异常，或没有收到开发者的确认，回调会进行退避的重试。 尽管如此，我们在接入时并不应该完全信任回调机制，需要根据业务情况使用查询接口，获取订单状态，及时的为消费者进行服务。 在交易完成后，可以在账户页的在途资金一栏中看到对应的货款。这时可以使用退款接口进行分账前退款，接口支持部分退款。在查询到退款状态变为 SUCCESS 后，该部分资金就会被推给消费者，且未收取手续费。 在经过 7 天后，资金可以进行分账。开发者通过调用分账接口将订单（包含部分退款的订单）进行分账，之后货款才会出现在账户页的可提现金额中，平台会在这一环节对交易手续费进行扣除，即到账金额为 订单金额 * (1 - 0.006)。 如果在此阶段仍需要对订单进行退款，需要在退款接口中，根据文档说明传入 all_settle = 1 后请求退款。这时退款会对账户的可提现金额进行扣除，因此分账后退款会产生手续费的折损。请尽量在交易确认后进行分账。
     *
     * 接口列表
     * 业务环节
     * 接口 接口名 说明
     * 支付
     * 服务端预下单 create_order 用于发起支付
     * 支付回调 N/A 用于接收支付的结果
     * 订单查询 query_order 用于查询订单状态
     * 退款
     * 退款请求 create_refund 用于发起退款
     * 退款回调 N/A 用于接收退款结果的回调 **注意：这里接收的退款到账结果，并不是退款请求的受理结果。
     * 退款查询 query_refund 用于查询退款受理和到账的状态
     * 分账
     * 分账请求 settle 用于将「在途资金」转入到「可提现资金」 **注意：所有用户支付完的款项，均会先进入「在途资金」，需调用分账才能转入「可提现资金」，交易款项并不会自动进入「可提现」
     * 分账回调 N/A 用于接收分账的结果及对应的费用组成（如手续费）
     * 分账查询 query_settle 用于主动查询分账的结果及对应的费用组成（如手续费）
     *
     * 支付下单
     * 服务端预下单
     * 接口地址：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/v1/create_order
     *
     * 输入
     *
     * 字段名 类型 是否必传 字段描述
     * app_id string 是 小程序APPID
     * out_order_no string 是 开发者侧的订单号, 同一小程序下不可重复
     * total_amount number 是 支付价格; 接口中参数支付金额单位为[分]
     * subject string 是 商品描述; 长度限制 128 字节，不超过 42 个汉字
     * body string 是 商品详情
     * valid_time number 是 订单过期时间(秒); 最小 15 分钟，最大两天
     * sign string 是 开发者对核心字段签名, 签名方式见文档附录, 防止传输过程中出现意外
     * cp_extra string 否 开发者自定义字段，回调原样回传
     * notify_url string 否 商户自定义回调地址
     * thirdparty_id string 否，服务商模式接入必传 第三方平台服务商 id，非服务商模式留空
     * disable_msg number 否 是否屏蔽担保支付的推送消息，1-屏蔽 0-非屏蔽，接入 POI 必传
     * msg_page string 否 担保支付消息跳转页
     * 输出 返回值为 JSON 形式，其中包括如下字段：
     *
     * 字段名 类型 描述
     * err_no number 状态码 0-业务处理成功
     * err_tips string 错误提示信息，常见错误处理可参考附录常见问题章节
     *
     * data orderInfo 拉起收银台的 orderInfo
     *
     * 将 data 中的 order_id 与 order_token 字段作为入参，在小程序内调用 tt.pay 即可拉起收银台完成支付。 DEMO
     *
     * curl --location --request POST 'developer.toutiao.com/api/apps/ecpay/v1/create_order' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "app_id": "tt07e3715e98c9aac0",
     * "out_order_no": "out_order_no_1",
     * "total_amount": 12800,
     * "subject": "测试商品",
     * "body": "测试商品",
     * "valid_time": 180,
     * "sign": "d716027b7b5a91a3319a061d818cc9cc",
     * "cp_extra": "一些附加信息",
     * "notify_url": "https://xxx.com/callback",
     * "disable_msg": 0,
     * "msg_page": "pages/index"
     * }'
     *
     * 一个典型的返回值
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "data": {
     * "order_id": "6819903302604491021",
     * "order_token": "CgwIARDiDRibDiABKAESTgpMbBhsCG7V1MPGAvpICgUSyGcuNOVb/BnCOi9EXgAxIxDqLTwCA6Hd3tNrCde28o0qjmAJQsmLrD18ifr5QktalszSSmTpHCqEm3h55xoA"
     * }
     * }
     *
     * 支付回调
     * 一个回调信息包含如下字段：
     *
     * 字段名 类型 描述
     * timestamp number Unix 时间戳，10 位，整型数
     * nonce string 随机数
     * msg string 订单信息的 json 字符串
     * type string 回调类型标记，支付成功回调为"payment"
     * msg_signature string 签名
     * 当订单成功支付之后，服务端会通过 post 方式回调开发者提供的 http 接口，使用 token 进行校验。回调信息包括 msg 信息为以下内容序列化得到的 json 字符串：
     *
     * 字段名 类型 描述
     * appid string 小程序 id
     * cp_orderno string 开发者传入订单号
     * way string way 字段中标识了支付渠道：2-支付宝，1-微信
     * cp_extra string 预下单时开发者传入字段
     * item_id string 订单来源视频对应视频 id
     * seller_uid string 该笔交易卖家商户号
     * extra string 该笔交易附加业务逻辑说明，例如 CPS 交易
     *
     * extra 典型值如下
     *
     * {\"cps_info\":\"poi\",\"share_amount\":\"299\"}
     *
     * 注 ：item_id 字段附加上报信息，所以首次回调时，该字段可能未写入系统，导致部分回调丢失 item_id，可以通过查询或返回失败，等等回调重试的方式补全数据。 在开发者服务端收到回调且处理成功后，需要按以下 json 返回表示处理成功，否则小程序服务端会认为通知失败进行重试。
     *
     * {
     * "err_no": 0,
     * "err_tips": "success"
     * }
     *
     * Demo 开发者可以使用如下的 curl 命令来校验自己的服务是否可以正确处理回调
     *
     * curl --location --request POST 'your callback url' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "timestamp": 1602507471,
     * "nonce": "797",
     * "msg": "{\"appid\":\"tt07e3715e98c9aac0\",\"cp_orderno\":\"out_order_no_1\",\"cp_extra\":\"\",\"way\":\"2\",\"payment_order_no\":\"2021070722001450071438803941\",\"total_amount\":9980,\"status\":\"SUCCESS\",\"seller_uid\":\"69631798443938962290\",\"extra\":\"null\",\"item_id\":\"\"}",
     * "msg_signature": "52fff5f7a4bf4a921c2daf83c75cf0e716432c73",
     * "type": "payment"
     * }'
     */
    public function createOrder($app_id, $out_order_no, $total_amount, $subject, $body, $valid_time, $cp_extra, $notify_url, $disable_msg, $msg_page)
    {
        $params = array();
        $params['app_id'] = $app_id;
        $params['out_order_no'] = $out_order_no;
        $params['total_amount'] = $total_amount;
        $params['subject'] = $subject;
        $params['body'] = $body;
        $params['valid_time'] = $valid_time;
        $params['cp_extra'] = $cp_extra;
        $params['notify_url'] = $notify_url;
        $params['disable_msg'] = $disable_msg;
        $params['msg_page'] = $msg_page;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/v1/create_order', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 订单查询
     * 接口地址：
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/v1/query_order
     *
     * 输入：
     *
     * 字段名 类型 是否必传 字段描述
     * app_id string 是 小程序APPID
     * out_order_no string 是 开发者侧的订单号, 同一小程序下不可重复
     * sign string 是 开发者对核心字段签名, 签名方式见文档附录, 防止传输过程中出现意外
     * thirdparty_id string 否，服务商模式接入必传 第三方平台服务商 id，非服务商模式留空
     *
     * 输出： 返回值为 JSON 形式，其中包括 payment_info 字段： way 字段中标识了支付渠道：2-支付宝，1-微信，3-银行卡 payment_info 返回值结构释义如下：
     *
     * {
     * "total_fee": 1200,
     * "order_status": "PROCESSING-处理中|SUCCESS-成功|FAIL-失败|TIMEOUT-超时",
     * "pay_time": "支付时间",
     * "way": 1,
     * "channel_no": "渠道单号",
     * "channel_gateway_no": "渠道网关号",
     * "seller_uid": "卖家商户号",
     * "item_id": "视频id"
     * }
     *
     * 注意：订单查询接口对单个 AppID 限流 30QPS。
     *
     * Demo:
     *
     * curl --location --request POST 'https://developer.toutiao.com/api/apps/ecpay/v1/query_order' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "app_id": "tt07e3715e98c9aac0",
     * "out_order_no": "out_order_no_1",
     * "sign": "569168789858734fecef2d5ae604ff1a",
     * }'
     *
     * 返回值:
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "out_order_no": "out_order_no_1",
     * "order_id": "6979643835974486313",
     * "payment_info": {
     * "total_fee": 68800,
     * "order_status": "SUCCESS",
     * "pay_time": "2021-07-01 01:43:15",
     * "way": 2,
     * "channel_no": "2021070122001432551415940569",
     * "channel_gateway_no": "12107010014028882037",
     * "seller_uid": "6943058549596520",
     * "item_id": "6943058549596520"
     * }
     * }
     */
    public function queryOrder($app_id, $out_order_no)
    {
        $params = array();
        $params['app_id'] = $app_id;
        $params['out_order_no'] = $out_order_no;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/v1/query_order', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 退款
     * 当交易发生之后一段时间内，由于买家或者卖家的原因需要退款时，卖家可以通过退款接口将支付款退还给买家，将在收到退款请求并且验证成功之后，按照退款规则将支付款按原路退到买家帐号上。 注意：在途资金中的所有货款均是与订单关联的，只有当该订单在途资金中剩余金额超过退款金额时，才可以进行在途资金的退款。否则，需要将 all_settle 字段置为 1，进行分账后退款。分账后退款会从账户的可提现金额中进行退款。 当可提现金额也不足退款金额时，会发生提交退款成功，但回调失败的情况。目前可提现金额没有充值渠道，为了避免出现订单无法退款的情况出现，请根据业务情况自行保留一部分可提现金额在系统中。
     *
     * 退款请求
     * POST https://developer.toutiao.com/api/apps/ecpay/v1/create_refund
     *
     * 输入：
     * 字段名 类型 是否必传 字段描述
     * app_id string 是 小程序APPID
     * out_order_no string 是 商户分配订单号，标识进行退款的订单
     * out_refund_no string 是 商户分配退款号
     * reason string 是 退款原因
     * refund_amount number 是 退款金额，单位[分]
     * sign string 是 开发者对核心字段签名, 签名方式见文档附录, 防止传输过程中出现意外
     * cp_extra string 否 开发者自定义字段，回调原样回传
     * notify_url string 否 商户自定义回调地址
     * thirdparty_id string 否，服务商模式接入必传 第三方平台服务商 id，非服务商模式留空
     * disable_msg number 否 是否屏蔽担保支付的推送消息，1-屏蔽 0-非屏蔽，接入 POI 必传
     * msg_page string 否 担保支付消息跳转页
     * all_settle number 否 是否为分账后退款，1-分账后退款；0-分账前退款。分账后退款会扣减可提现金额，请保证余额充足
     *
     * 输出：
     *
     * 字段名 类型 描述
     * err_no number 状态码 0-业务处理成功
     * err_tips string 错误提示信息，常见错误处理可参考附录常见问题章节
     * refund_no string 担保交易服务端退款单号
     *
     * Demo：
     *
     * {
     * "app_id": "tt07e3715e98c9aac0",
     * "out_order_no": "out_order_no_1",
     * "out_refund_no": "out_refund_no_1",
     * "total_amount": 19800,
     * "refund_amount": 19800,
     * "reason": "订单退款",
     * "cp_extra": "",
     * "notify_url": "",
     * "sign": "6eeb2a4d336f9c7c38f05acb598b7dcc",
     * "thirdparty_id": "",
     * "disable_msg": 0,
     * "msg_page": "",
     * "all_settle": 0
     * }
     *
     * 退款回调
     * 当订单成功退款之后，服务端会通过 post 方式回调开发者提供的 http 接口，使用 token 进行校验。由于网络波动等原因，可能会产生重复的通知消息，接入方需要正确处理。 退款结构体与支付回调相同，type 枚举值为 refund 标记该回调为 refund。msg 中的 json 字符串信息包括：
     *
     * app_id: 小程序 appid
     * cp_refundno:开发者自定义的退款单号
     * cp_extra:开发者传的额外参数
     * status:退款状态 PROCESSING-处理中|SUCCESS-成功|FAIL-失败
     * refund_amount: 退款金额 在开发者服务端收到回调且处理成功后，需要按以下 json 返回表示处理成功，否则小程序服务端会认为通知失败进行重试。
     * {
     * "err_no": 0,
     * "err_tips": "success"
     * }
     *
     * demo 开发者可以使用如下的 curl 命令来校验自己的服务是否可以正确处理回调
     *
     * curl --location --request POST 'your callback url' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "timestamp": 1602507471,
     * "nonce": "797",
     * "msg": "{\"appid\":\"tt07e3715e98c9aac0\",\"cp_refundno\":\"out_refund_no_1\",\"cp_extra\":\"\",\"status\":\"SUCCESS\",\"refund_amount\":13800}",
     * "type": "refund",
     * "msg_signature":"b313c64257660defba884af0e83be4d79794b559"
     * }'
     */
    public function createRefund($app_id, $out_order_no, $out_refund_no, $total_amount, $refund_amount, $reason, $cp_extra, $notify_url, $thirdparty_id, $disable_msg, $msg_page, $all_settle)
    {
        $params = array();
        $params['app_id'] = $app_id;
        $params['out_order_no'] = $out_order_no;
        $params['out_refund_no'] = $out_refund_no;
        $params['total_amount'] = $total_amount;
        $params['refund_amount'] = $refund_amount;
        $params['reason'] = $reason;
        $params['cp_extra'] = $cp_extra;
        $params['notify_url'] = $notify_url;
        $params['thirdparty_id'] = $thirdparty_id;
        $params['disable_msg'] = $disable_msg;
        $params['msg_page'] = $msg_page;
        $params['all_settle'] = $all_settle;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/v1/create_refund', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 查询退款
     * POST https://developer.toutiao.com/api/apps/ecpay/v1/query_refund
     *
     * 输入：
     * 字段名类型 是否必传 字段描述
     * app_id string 是 小程序APPID
     * out_refund_no string 是 开发者侧的退款号
     * sign string 是 开发者对核心字段签名, 签名方式见文档附录, 防止传输过程中出现意外
     * thirdparty_id string 否，服务商模式接入必传 第三方平台服务商 id，非服务商模式留空
     *
     * 输出 返回值为 JSON 形式，整体返回值结构如下：
     * 字段名 类型 描述
     * err_no number 状态码 0-业务处理成功
     * err_tips string 错误提示信息，常见错误处理可参考附录常见问题章节
     *
     * refundInfo refundInfo 订单退款基本信息
     * 其中 refund_info 包含如下的字段：
     * 字段名类型 描述
     * refund_no string 担保支付侧的退款单号
     * refund_amount number 退款金额，单位[分]
     * refund_status string 退款状态，成功-SUCCESS；失败-FAIL
     *
     * 注意：查询退款接口对单个 AppID 限流 30QPS。
     *
     * demo
     *
     * curl --location --request POST 'developer.toutiao.com/api/apps/ecpay/v1/query_refund' \
     * --header 'Content-Type: application/json' \
     * --header 'Cookie: sessionid=c44a6d4d444357b39913fd22812ecad5' \
     * --data-raw '{
     * "app_id": "tt07e3715e98c9aac0",
     * "out_refund_no": "out_refund_no_1",
     * "sign": "47d7b7c65b9d2d6a1142e84c7ee202e5"
     * }'
     *
     * 一个典型的返回值如下
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "refundInfo": {
     * "refund_no": "69821555158047006",
     * "refund_amount": 3580,
     * "refund_status": "SUCCESS"
     * }
     * }
     */
    public function queryRefund($app_id, $out_refund_no)
    {
        $params = array();
        $params['app_id'] = $app_id;
        $params['out_refund_no'] = $out_refund_no;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/v1/query_refund', $params, array());
        return $this->_client->rst($rst);
    }
    /**
     * 分账
     * 分账用于确认一笔在途资金，将其转化为可提现资金。无论是否有分账方都需要主动的调用接口才能进行资金的提现
     *
     * 分账请求
     * 分账功能用于在交易发生之后一段时间后，可以根据需求分配货款，将资金从在途资金账户转移至可提现账户。为了保证业务正确处理, 请按担保交易设置页面的分账周期处理分账. 订单在支付后 150 天后如果仍然未进行分账，则会自动分配全部货款给卖家。在分账环节中，小程序平台会参与对整笔交易的手续费进行扣除，详情可以参考附录手续费计算规则。手续费默认由卖家承担。目前担保支付只支持单次的分账，会自动将未指定的货款分配给卖家账户。 接口地址
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/v1/settle
     *
     * 输入
     * 字段名 类型 是否必传 字段描述
     * app_id string 是 小程序APPID
     * out_settle_no string 是 开发者侧的结算号, 不可重复
     * out_order_no string 是 商户分配订单号，标识进行结算的订单
     * settle_desc string 是 结算描述，长度限制 80 个字符
     * settle_params string 否 其他分账方信息，分账分配参数 SettleParameter 数组序列化后生成的 json 格式字符串
     * sign string 是 开发者对核心字段签名, 签名方式见文档附录, 防止传输过程中出现意外
     * cp_extra string 否 开发者自定义字段，回调原样回传
     * notify_url string 否 商户自定义回调地址
     * thirdparty_id string 否，服务商模式接入必传 第三方平台服务商 id，非服务商模式留空
     *
     * 分润方参数 SettleParameter，只需要传入卖家之外的分润方
     *
     * {
     * "merchant_uid": "分账方商户号",
     * "amount": 10 // 分账金额
     * }
     *
     * 输出
     * 字段名 类型 描述
     * err_no number 状态码 0-业务处理成功
     * err_tips string 错误提示信息，常见错误处理可参考附录常见问题章节
     * settle_no string 平台生成分账单号
     *
     * Demo
     *
     * curl --location --request POST 'http://developer.toutiao.com/api/apps/ecpay/v1/settle' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "out_settle_no": "out_settle_no_1",
     * "out_order_no": "out_order_no_1",
     * "settle_desc": "分账",
     * "notify_url": "https://your.callback.url",
     * "cp_extra": "2856",
     * "app_id": "tt07e3715e98c9aac0",
     * "sign": "d98e6af1c490b36f7b72e2037f81a511",
     * "settle_params": "[{\"merchant_uid\":\"696458350318359362\",\"amount\":60}]"
     * }'
     *
     * 分账回调
     * 当订单成功分账之后，服务端会通过 post 方式回调开发者提供的 http 接口，使用 token 进行校验。回调结构体与支付相同，回调类型枚举 type 值为 settle。msg 字段的 json 转义字符串包含如下内容：
     *
     * appid:小程序 id
     * cp_settle_no 开发者自定义的分账单号
     * cp_extra 开发者传的额外参数
     * status 分账状态，PROCESSING-处理中|SUCCESS-成功|FAIL-失败
     * rake 该笔交易分账环境收取的手续费金额
     * commission 交易参与 CPS 投放等任务时，产生的佣金 在开发者服务端收到回调且处理成功后，需要按以下 json 返回表示处理成功，否则小程序服务端会认为通知失败进行重试。
     * {
     * "err_no": 0,
     * "err_tips": "success"
     * }
     *
     * demo 开发者可以使用如下的 curl 命令来校验自己的服务是否可以正确处理回调
     *
     * curl --location --request POST 'your callback url' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "timestamp": 1602507471,
     * "nonce": "797",
     * "msg": "{\"appid\":\"tt07e3715e98c9aac0\",\"cp_settle_no\":\"out_settle_no_1\",\"cp_extra\":\"2856\",\"status\":\"SUCCESS\",\"rake\":95,\"commission\":0}",
     * "type": "settle",
     * "msg_signature":"b313c64257660defba884af0e83be4d79794b559"
     * }'
     */
    public function settle($out_settle_no, $out_order_no, $settle_desc, $notify_url, $cp_extra, $app_id, $settle_params)
    {
        $params = array();
        $params['out_settle_no'] = $out_settle_no;
        $params['out_order_no'] = $out_order_no;
        $params['settle_desc'] = $settle_desc;
        $params['notify_url'] = $notify_url;
        $params['cp_extra'] = $cp_extra;
        $params['app_id'] = $app_id;
        $params['settle_params'] = \json_encode($settle_params, JSON_UNESCAPED_UNICODE);
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/v1/settle', $params, array());
        return $this->_client->rst($rst);
    }

    /**
     * 查询分账
     * 接口地址:
     *
     * POST https://developer.toutiao.com/api/apps/ecpay/v1/query_settle
     *
     * 输入：
     *
     * 字段名 类型 是否必传 字段描述
     * app_id string 是 小程序APPID
     * out_settle_no string 是 开发者侧的分账号
     * sign string 是 开发者对核心字段签名, 签名方式见文档附录, 防止传输过程中出现意外
     * thirdparty_id string 否，服务商模式接入必传第三方平台服务商 id，非服务商模式留空
     *
     * 输出：
     * 字段名 类型 描述
     * err_no number 状态码 0-业务处理成功
     * err_tips string 错误提示信息，常见错误处理可参考附录常见问题章节
     * settle_info settle_info 相关分账单信息
     * 其中 settle_info 包含如下的字段：
     * 字段名 类型 描述
     * settle_no string 担保支付侧的分账单号
     * settle_amount number 分账金额，单位[分]
     * settle_status string 分账状态，成功-SUCCESS；失败-FAIL
     * 注意：查询分账接口对单个 AppID 限流 30QPS。
     *
     * Demo
     *
     * curl --location --request POST 'http://developer.toutiao.com/api/apps/ecpay/v1/query_settle' \
     * --header 'Content-Type: application/json' \
     * --data-raw '{
     * "app_id": "tt07e3715e98c9aac0",
     * "out_settle_no": "out_settle_no_1",
     * "sign": "ad484fa21a1fd788490c25ac3779a73a"
     * }'
     *
     * 一个典型的返回值：
     *
     * {
     * "err_no": 0,
     * "err_tips": "",
     * "settle_info": {
     * "settle_no": "69822583687211557",
     * "settle_amount": 5990,
     * "settle_status": "SUCCESS"
     * }
     * }
     */
    public function querySettle($app_id, $out_settle_no)
    {
        $params = array();
        $params['app_id'] = $app_id;
        $params['out_settle_no'] = $out_settle_no;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());
        $rst = $this->_request->post($this->_url . 'ecpay/v1/query_settle', $params, array());
        return $this->_client->rst($rst);
    }

    /**
     * https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/ecpay/bill
     * 担保交易账单查询
     * 商户可以通过该接口查看担保交易历史账单，进行账单校对。
     *
     * 请求地址
     * GET https://developer.toutiao.com/api/apps/bill
     * 请求签名
     * 请求需要签名，详见签名使用
     *
     * 请求参数
     * 属性 数据类型 必填 说明
     * start_date string 是 开始时间，格式：20210603
     * end_date string 是 结束时间，格式：20210603
     * seller string 是 商户号
     * bill_type string 是 账单类型，包括 payment:支付账单, settle:分账账单, refund:退款账单
     * app_id string 是 和商户号绑定的 appid 或者 thirdparty_id
     * sign string 是 对上面的参数进行签名
     * 返回值
     * 当请求成功时，数据以表格形式返回，第一行为各列字段名，根据请求的账单类型不同而不同，由bill_type决定，目前有支付账单，分账账单，退款账单。
     *
     * 支付账单：
     *
     * 序号, 支付时间, 商户号, openId, 订单号, 平台订单号, 渠道订单号, 订单金额, 支付方式, 支付状态
     *
     * 分账账单：
     *
     * 序号, 分账时间, 商户号, openId, 订单号, 平台订单号, 渠道订单号, 订单金额, 支付方式, 分账单号, 分账状态, 分账方和分账金额, 技术服务费, 抽佣金额
     *
     * 退款账单：
     *
     * 序号, 退款时间, 商户号, openId, 订单号, 渠道订单号, 订单金额, 支付方式, 退款类型, 退款单号, 平台退款单号, 退款状态, 退款完成时间, 退款金额
     *
     * 第二行起，为数据记录
     *
     * 序号 支付时间 商户号 openId 订单号 平台订单号 渠道订单号 订单金额 支付方式 支付状态
     * errCode
     * 当请求失败时，会返回非 0 的 err_no，错误信息会携带在 err_tips 中，错误码含义如下：
     *
     * 错误号 描述
     * 1 参数错误
     * -1 系统错误
     * 请求示例
     * https://developer.toutiao.com/api/apps/bill?start_date=20210604&end_date=20210603&seller=xxx&bill_type=settle&app_id=xx&sign=****
     *
     * 返回示例
     * 错误返回
     *
     * {
     * "err_no": 1,
     * "err_tips": "end_date is before start_date"
     * }
     */
    public function bill($start_date, $end_date, $seller, $bill_type, $app_id)
    {
        $params = array();
        $params['start_date'] = $start_date;
        $params['end_date'] = $end_date;
        $params['seller'] = $seller;
        $params['bill_type'] = $bill_type;
        $params['app_id'] = $app_id;
        $params['sign'] = Helpers::sign($params, $this->getPaymentSalt());

        $rst = $this->_request->get($this->_url . 'bill', $params);
        return $this->_client->rst($rst);
    }
}
