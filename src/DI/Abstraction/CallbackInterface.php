<?php

namespace DI\Abstraction;

interface CallbackInterface 
{
    /**
     * @param callable $callable
     *
     * @return CallbackInterface
     */
    public function setCallback($callable);

    /**
     * @return array
     */
    public function getParams();

    /**
     * @param array $params
     *
     * @return CallbackInterface
     */
    public function setParams($params);

    /**
     * @return mixed
     */
    public function execute();
}
