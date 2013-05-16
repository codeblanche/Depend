<?php

namespace Depend;

use Depend\Abstraction\InjectorInterface;

class InjectorFactory
{
    /**
     * @var InjectorInterface
     */
    protected $injectorPrototype;

    public function __construct(InjectorInterface $injectorPrototype)
    {
        $this->injectorPrototype = $injectorPrototype;
    }

    /**
     * @param string $methodName
     * @param mixed  $value
     *
     * @return InjectorInterface
     */
    public function create($methodName, $value)
    {
        $newInstance = clone $this->injectorPrototype;

        return $newInstance->setMethodName($methodName)->setParams(array($value));
    }
}
