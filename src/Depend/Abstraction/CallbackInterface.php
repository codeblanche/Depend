<?php

namespace Depend\Abstraction;

interface CallbackInterface extends ActionInterface
{
    /**
     * @param callable $callable
     *
     * @return ActionInterface
     */
    public function setCallback($callable);

    /**
     * @return array
     */
    public function getParams();

    /**
     * @param array $params
     *
     * @return ActionInterface
     */
    public function setParams($params);
}
