<?php

namespace Bytedance\Toutiao\Model;

/**
 * 基础model
 */
abstract class Base
{

    public function getParams()
    {
        $params = array();
        return $params;
    }

    protected function isNotNull($var)
    {
        return !is_null($var);
    }
}
