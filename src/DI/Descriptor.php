<?php

namespace DI;

use DI\Abstraction\DescriptorInterface;
use ReflectionClass;

class Descriptor implements DescriptorInterface
{
    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var boolean
     */
    protected $isShared = true;

    /**
     * @var boolean
     */
    protected $isCloneable = true;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var \ReflectionMethod
     */
    protected $reflectionConstructor;

    /**
     * @var \ReflectionParameter[]
     */
    protected $constructorParams = array();

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $paramNames = array();

    /**
     * @var array
     */
    protected $callbacks = array();

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Manager $manager
     *
     * @return DescriptorInterface
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @param boolean $value
     *
     * @return DescriptorInterface
     */
    public function setIsCloneable($value)
    {
        $this->isCloneable = (boolean) $value;

        return $this;
    }

    /**
     * @param boolean $value
     *
     * @return DescriptorInterface
     */
    public function setIsShared($value)
    {
        $this->isShared = (boolean) $value;

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
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $value
     *
     * @return DescriptorInterface
     */
    public function setParams(array $value)
    {
        $this->params = $value;

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
     * @return string
     */
    public function getName()
    {
        return $this->reflectionClass->getName();
    }

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * @return boolean
     */
    public function isCloneable()
    {
        return $this->isCloneable;
    }

    /**
     * @return boolean
     */
    public function isShared()
    {
        return $this->isShared;
    }

    /**
     * Load a class using its ReflectionClass object.
     *
     * @param ReflectionClass $class
     *
     * @throws \Exception|\ReflectionException
     * @return DescriptorInterface
     */
    public function load(ReflectionClass $class)
    {
        $this->reset();

        $this->reflectionClass = $class;

        if (!$this->reflectionClass->isInstantiable()) {
            return $this;
        }

        $this->reflectionConstructor = $this->reflectionClass->getConstructor();

        if (is_null($this->reflectionConstructor)) {
            return $this;
        }

        $params = $this->reflectionConstructor->getParameters();

        if (is_null($params)) {
            return $this;
        }

        /** @var $param \ReflectionParameter */
        foreach ($params as $param) {
            $paramName                = $param->getName();
            $this->params[$paramName] = $this->resolveArgumentValue($param);
            $this->paramNames[]       = $paramName;
        }

        $this->constructorParams = $params;

        return $this;
    }

    /**
     * Reset all properties
     */
    protected function reset()
    {
        $this->params                = array();
        $this->isShared              = true;
        $this->isCloneable           = true;
        $this->reflectionClass       = null;
        $this->reflectionConstructor = null;
        $this->constructorParams     = array();
    }

    /**
     * @param \ReflectionParameter $param
     *
     * @return mixed
     */
    protected function resolveArgumentValue(\ReflectionParameter $param)
    {
        $paramClass = $param->getClass();

        if ($paramClass instanceof ReflectionClass) {
            $descriptor = clone $this;

            $descriptor->load($paramClass);

            return $descriptor;
        }

        return null;
    }

    /**
     * Set one of the constructor parameters by identifier (index or name).
     *
     * @param int|string $identifier
     * @param mixed      $value
     *
     * @return DescriptorInterface
     */
    public function param($identifier, $value)
    {
        // TODO: Implement param() method.
    }

    /**
     * Call a method after instantiation with the specified parameters.
     *
     * @param callable|string $callback
     * @param array           $params
     *
     * @return mixed
     */
    public function call($callback, array $params)
    {
        // TODO: Implement call() method.
    }
}
