<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;
use Depend\Abstraction\DescriptorInterface;
use Depend\Exception\InvalidArgumentException;
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
    protected $actions = array();

    /**
     * @var string
     */
    protected $name;

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
     * @param array $value
     *
     * @return DescriptorInterface
     */
    public function setParams($value)
    {
        if (!is_array($value)) {
            return $this;
        }

        foreach ($value as $identifier => $param) {
            $this->setParam($identifier, $param);
        }

        return $this;
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
     * @return Descriptor
     */
    public function load(ReflectionClass $class)
    {
        $this->reset();

        $this->reflectionClass = $class;
        $this->name            = $this->reflectionClass->getName();

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
        $paramClass = null;

        try {
            $paramClass = $param->getClass();
        }
        catch (\ReflectionException $e) {}

        if (!($paramClass instanceof ReflectionClass)) {
            return $this->resolveArgumentDefaultValue($param);
        }

        return $this->manager->describe($paramClass->getName(), null, null, $paramClass);
    }

    protected function resolveArgumentDefaultValue(\ReflectionParameter $param)
    {
        try {
            return $param->getDefaultValue();
        }
        catch(\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Set one of the constructor parameters by identifier (index or name).
     *
     * @param int|string $identifier
     * @param mixed      $value
     *
     * @return Descriptor
     */
    public function setParam($identifier, $value)
    {
        if (is_numeric($identifier) && isset($this->paramNames[$identifier])) {
            $identifier = $this->paramNames[$identifier];
        }

        $this->params[$identifier] = $value;

        return $this;
    }

    /**
     * Execute the given callback after class instance is created.
     *
     * @param ActionInterface $action
     *
     * @return Descriptor
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @param ActionInterface[] $actions
     *
     * @throws Exception\InvalidArgumentException
     * @return Descriptor
     */
    public function setActions($actions)
    {
        if (!is_array($actions)) {
            return $this;
        }

        $this->actions = $actions;

        return $this;
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param string $name
     *
     * @return Descriptor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @param array  $params
     * @param array  $actions
     *
     * @return Descriptor
     */
    public function alias($name, $params = null, $actions = null)
    {
        return $this->manager->alias($name, $this, $params, $actions);
    }
}
