<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;
use Depend\Abstraction\DescriptorInterface;
use Depend\Exception\RuntimeException;
use ReflectionClass;
use SplObjectStorage;

class Descriptor implements DescriptorInterface
{
    /**
     * @var array
     */
    protected $actions = array();

    /**
     * @var \ReflectionParameter[]
     */
    protected $constructorParams = array();

    /**
     * @var boolean
     */
    protected $isCloneable = false;

    /**
     * @var boolean
     */
    protected $isShared = true;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $paramNames = array();

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var \ReflectionMethod
     */
    protected $reflectionConstructor;

    /**
     * @var Descriptor
     */
    protected $parent;

    /**
     * @var Descriptor[]
     */
    protected $interfaces = array();

    /**
     * @var SplObjectStorage
     */
    protected static $queue;

    /**
     * Default constructor
     */
    function __construct()
    {
        self::$queue = new SplObjectStorage();
    }

    /**
     * Execute the given callback after class instance is created.
     *
     * @param ActionInterface $action
     *
     * @return Descriptor
     */
    public function addAction($action)
    {
        if (!$action instanceof ActionInterface) {
            return $this;
        }

        $name = $action->getIdentifier();

        if (empty($name)) {
            return $this;
        }

        $this->actions[] = $action;

        return $this;
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

    /**
     * @return ActionInterface[]
     */
    public function getActions()
    {
        if (self::$queue->contains($this)) {
            return array();
        }

        self::$queue->attach($this);

        $actions = $this->actions;

        if (is_array($this->interfaces)) {
            /** @var $interface Descriptor */
            foreach ($this->interfaces as $interface) {
                $actions = array_merge($interface->getActions(), $actions);
            }
        }

        if ($this->parent instanceof Descriptor) {
            $actions = array_merge($this->parent->getActions(), $actions);
        }

        self::$queue->detach($this);

        return $actions;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = $this->params;

        if ($this->parent instanceof Descriptor) {
            $params = array_replace($this->parent->getParams(), $params);
        }

        return $params;
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
        if (!$this->isCloneable) {
            return false;
        }

        if ($this->parent && !$this->parent->isCloneable()) {
            return false;
        }

        if (is_array($this->interfaces)) {
            /** @var $interface Descriptor */
            foreach ($this->interfaces as $interface) {
                if ($interface->getName() === $this->getName()) {
                    continue;
                }

                if (!$interface->isCloneable()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function isShared()
    {
        if (!$this->isShared) {
            return false;
        }

        if ($this->parent && !$this->parent->isShared()) {
            return false;
        }

        if (is_array($this->interfaces)) {
            /** @var $interface Descriptor */
            foreach ($this->interfaces as $interface) {
                if ($interface->getName() === $this->getName()) {
                    continue;
                }

                if (!$interface->isShared()) {
                    return false;
                }
            }
        }

        return true;
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

        $this->interfaces            = $this->resolveInterfaces();
        $this->parent                = $this->resolveParent();
        $this->reflectionConstructor = $this->reflectionClass->getConstructor();

        if (is_null($this->reflectionConstructor)) {
            return $this;
        }

        $params = $this->reflectionConstructor->getParameters();

        if (empty($params)) {
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
     * @return Descriptor[]
     */
    protected function resolveInterfaces()
    {
        $interfaces = array();

        foreach ($this->reflectionClass->getInterfaceNames() as $interface) {
            $interfaces[] = $this->manager->describe($interface);
        }

        return $interfaces;
    }

    /**
     * @return Descriptor
     */
    protected function resolveParent()
    {
        $parent = $this->reflectionClass->getParentClass();

        if (!$parent instanceof ReflectionClass) {
            return null;
        }

        return $this->manager->describe($parent->getName(), null, null, $parent);
    }

    /**
     * @param ActionInterface[] $actions
     *
     * @return Descriptor
     */
    public function setActions($actions)
    {
        if (!is_array($actions)) {
            return $this;
        }

        foreach ($actions as $action) {
            $this->addAction($action);
        }

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
     * Set one of the constructor parameters by identifier (index or name).
     *
     * @param int|string $identifier
     * @param mixed      $value
     *
     * @return Descriptor
     */
    public function setParam($identifier, $value)
    {
        $this->params[$this->resolveParamName($identifier)] = $value;

        return $this;
    }

    /**
     * Resolve the parameter name
     *
     * @param int|string $identifier
     *
     * @return string|int
     */
    public function resolveParamName($identifier)
    {
        if (!isset($this->paramNames[$identifier])) {
            return $identifier;
        }

        return $this->paramNames[$identifier];
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
        $this->parent                = null;
        $this->interfaces            = null;
    }

    /**
     * @param \ReflectionParameter $param
     *
     * @return mixed
     */
    protected function resolveArgumentDefaultValue(\ReflectionParameter $param)
    {
        if (!$param->isDefaultValueAvailable()) {
            return null;
        }

        return $param->getDefaultValue();
    }

    /**
     * @param \ReflectionParameter $param
     *
     * @throws Exception\RuntimeException
     * @return mixed
     */
    protected function resolveArgumentValue(\ReflectionParameter $param)
    {
        $paramClass = null;

        try {
            $paramClass = $param->getClass();
        }
        catch (\ReflectionException $e) {
        }

        if (!($paramClass instanceof ReflectionClass) || $param->isDefaultValueAvailable()) {
            return $this->resolveArgumentDefaultValue($param);
        }

        if (!($this->manager instanceof Manager)) {
            throw new RuntimeException("Unable to retrieve descriptor for class '{$paramClass->getName(
                                       )}' " . "because the manager has not been set. Please use Descriptor::setManager to resolve this.");
        }

        return $this->manager->describe($paramClass->getName(), null, null, $paramClass);
    }
}
