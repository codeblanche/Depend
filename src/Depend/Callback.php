<?php

namespace Depend;

use Depend\Abstraction\CallbackInterface;
use Depend\Exception\RuntimeException;

class Callback implements CallbackInterface
{
    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var callable
     */
    protected $callable;

    /**
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function execute()
    {
        if (!is_callable($this->callable)) {
            throw new RuntimeException('Expected a callable method or function');
        }

        return call_user_func_array($this->callable, $this->params);
    }

    /**
     * @param callable $callable
     *
     * @return CallbackInterface
     */
    public function setCallback($callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return CallbackInterface
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
}
