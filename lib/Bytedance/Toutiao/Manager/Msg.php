<?php

/**
 * 消息控制器
 * @author guoyongrong <handsomegyr@126.com>
 *
 */

namespace Bytedance\Toutiao\Manager;

use Bytedance\Toutiao\Client;
use Bytedance\Toutiao\Manager\Msg\Template;

class Msg
{
    private $_client;

    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    /**
     * 获取模板消息管理器
     *
     * @return \Bytedance\Toutiao\Manager\Msg\Template
     */
    public function getTemplateSender()
    {
        return new Template($this->_client);
    }
}
