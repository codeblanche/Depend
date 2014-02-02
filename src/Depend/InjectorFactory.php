<?php

namespace Depend;

use Depend\Abstraction\InjectorInterface;

class InjectorFactory
{
    /**
     * @var InjectorInterface
     */
    protected $injectorPrototype;

    public function __construct(InjectorInterface $injectorPrototype = null)
    {
        if (!($injectorPrototype instanceof InjectorInterface)) {
            $injectorPrototype = new Injector();
        }

        $this->injectorPrototype = $injectorPrototype;
    }

    /**
     * @param string $methodName
     * @param mixed  $params
     *
     * @return InjectorInterface
     */
    public function create($methodName, $params)
    {
        $newInstance = clone $this->injectorPrototype;

        if (!is_array($params)) {
            $params = array($params);
        }

        return $newInstance->setMethodName($methodName)->setParams($params);
    }
}
