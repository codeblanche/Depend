<?php

namespace Depend;

use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\FactoryInterface;
use Depend\Exception\InvalidArgumentException;

class Factory implements FactoryInterface
{
    /**
     * @var object[]
     */
    protected $instances = array();

    /**
     * @param DescriptorInterface $descriptor
     *
     * @return object
     */
    public function create(DescriptorInterface $descriptor)
    {
        if ($descriptor->isShared()) {
            return $this->get($descriptor);
        }

        if ($descriptor->isCloneable()) {
            return clone $this->get($descriptor);
        }

        return $this->get($descriptor, true);
    }

    /**
     * @param DescriptorInterface $descriptor
     * @param bool                $new
     *
     * @return object
     */
    protected function get(DescriptorInterface $descriptor, $new = false)
    {
        $reflectionClass = $descriptor->getReflectionClass();
        $class           = $reflectionClass->getName();
        $params          = $descriptor->getParams();

        if (!isset($this->instances[$class]) || $new === true) {
            $this->instances[$class] = $reflectionClass->newInstanceWithoutConstructor();
            $constructor             = $this->resolveConstructor($reflectionClass);

            if ($constructor instanceof \ReflectionMethod) {
                $constructor->invokeArgs($this->instances[$class], $this->resolveDescriptors($params));
            }
        }

        // TODO: check for callbacks and execute them - also resolveDescriptors for callback parameters.

        return $this->instances[$class];
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return \ReflectionMethod|null
     */
    protected function resolveConstructor(\ReflectionClass $reflectionClass)
    {
        $constructor = null;

        if ($reflectionClass->isInstantiable()) {
            $constructor = $reflectionClass->getConstructor();
        }

        return $constructor;
    }

    /**
     * @param array $params
     *
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    protected function resolveDescriptors($params)
    {
        if (!is_array($params)) {
            throw new InvalidArgumentException('Expected an array.');
        }

        foreach ($params as &$param) {
            if ($param instanceof DescriptorInterface) {
                $param = $this->create($param);
            }
        }

        return $params;
    }
}
