<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;
use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\FactoryInterface;
use Depend\Abstraction\InjectorInterface;
use Depend\Exception\InvalidArgumentException;
use Depend\Exception\RuntimeException;

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
     * @throws Exception\RuntimeException
     * @return object
     */
    protected function get(DescriptorInterface $descriptor, $new = false)
    {
        $reflectionClass = $descriptor->getReflectionClass();
        $name            = $descriptor->getName();
        $class           = $reflectionClass->getName();
        $params          = $descriptor->getParams();

        if (!isset($this->instances[$class]) || $new === true) {
            try {
                $this->instances[$name] = false;
                $args                    = $this->resolveDescriptors($params);
                $instance                = $reflectionClass->newInstanceArgs($args);
                $this->instances[$name] = $instance;

                $this->executeActions($instance, $descriptor->getActions());
            }
            catch (RuntimeException $e) {
                if ($e->getCode() === 255) {
                    throw $e;
                }

                throw new RuntimeException($e->getMessage(
                ) . " in '$class'. Please use a dependency setter method to resolve this.", 255);
            }
        }

        if ($this->instances[$name] === false) {
            throw new RuntimeException("Circular dependency found for class '$class'");
        }

        return $this->instances[$name];
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

    /**
     * @param $object
     * @param $actions
     */
    protected function executeActions($object, $actions)
    {
        if (!is_array($actions) || empty($actions)) {
            return;
        }

        foreach ($actions as $action) {
            if (!($action instanceof ActionInterface)) {
                continue;
            }

            if ($action instanceof InjectorInterface) {
                $action->setParams($this->resolveDescriptors($action->getParams()));
            }

            $action->execute($object);
        }
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
}
