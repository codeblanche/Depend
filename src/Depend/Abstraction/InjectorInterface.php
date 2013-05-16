<?php

namespace Depend\Abstraction;

interface InjectorInterface extends ActionInterface
{
    /**
     * @param string $methodName
     *
     * @return InjectorInterface
     */
    public function setMethodName($methodName);

    /**
     * @return array
     */
    public function getParams();

    /**
     * @param array $params
     *
     * @return InjectorInterface
     */
    public function setParams($params);
}
