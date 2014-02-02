<?php

namespace Depend;

use Depend\Abstraction\InjectorInterface;
use Depend\Exception\RuntimeException;

class Injector implements InjectorInterface
{
    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @param object $object
     *
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function execute($object)
    {
        $callable = array($object, $this->methodName);

        if (!is_callable($callable)) {
            $className = get_class($object);

            throw new RuntimeException("Method '$this->methodName' does not exist in object of class '$className'");
        }

        return call_user_func_array($callable, $this->params);
    }

    /**
     * @param string $methodName
     *
     * @return Injector
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;

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
     * @return Injector
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Returns an unique identifier for the function/method
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->methodName;
    }
}
