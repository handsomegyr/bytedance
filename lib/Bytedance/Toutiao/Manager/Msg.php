<?php

/**
 * 消息控制器
 * @author guoyongrong <handsomegyr@126.com>
 *
 */

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;
use Bytedance\Toutiao\Manager\Msg\SubscribeNotification;

class Msg
{
    private $_client;
    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    /**
     * 获取订阅消息管理器
     *
     * @return \Bytedance\Toutiao\Manager\Msg\SubscribeNotification
     */
    public function getSubscribeNotificationManager()
    {
        return new SubscribeNotification($this->_client);
    }
}
