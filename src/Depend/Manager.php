<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;
use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\InjectorInterface;
use Depend\Abstraction\ModuleInterface;
use Depend\Exception\CircularReferenceException;
use Depend\Exception\InvalidArgumentException;
use ReflectionClass;
use RuntimeException;
use SplObjectStorage;

class Manager
{
    /**
     * @var DescriptorInterface
     */
    protected $descriptorPrototype;

    /**
     * @var DescriptorInterface[]
     */
    protected $descriptors = array();

    /**
     * @var object[]
     */
    protected $instances = array();

    /**
     * @var DescriptorInterface[]
     */
    protected $named = array();

    /**
     * @var array Queue to aid in the fight against circular dependencies.
     */
    protected $queue = array();

    /**
     * @var SplObjectStorage
     */
    protected $deferredActions;

    /**
     * @param DescriptorInterface $descriptorPrototype
     */
    public function __construct(DescriptorInterface $descriptorPrototype = null)
    {
        if (!($descriptorPrototype instanceof DescriptorInterface)) {
            $descriptorPrototype = new Descriptor;
        }

        $this->deferredActions     = new SplObjectStorage();
        $this->descriptorPrototype = $descriptorPrototype;

        $this->descriptorPrototype->setManager($this);

        $this->implement('Depend\Abstraction\DescriptorInterface', 'Depend\Descriptor')->setIsShared(false);
        $this->implement('Depend\Abstraction\InjectorInterface', 'Depend\Injector');
        $this->describe('Depend\Manager');
        $this->set('Depend\Manager', $this);
        $this->set('Depend\Descriptor', $descriptorPrototype);
    }

    /**
     * Register a module object or class to register it's own dependencies.
     *
     * @param ModuleInterface|string $module
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function module($module)
    {
        if (is_object($module)) {
            if (!($module instanceof ModuleInterface)) {
                $moduleType = get_class($module);

                throw new InvalidArgumentException("Given module object '$moduleType' does not implement 'Depend\\Abstraction\\ModuleInterface'");
            }

            $module->register($this);

            return $this;
        }

        if (!class_exists((string) $module)) {
            throw new InvalidArgumentException("Given class name '$module' could not be found");
        }

        return $this->module(new $module);
    }

    /**
     * Add a class descriptor to the managers collection.
     *
     * @param Abstraction\DescriptorInterface $descriptor
     */
    public function add(DescriptorInterface $descriptor)
    {
        $key = $this->makeKey($descriptor->getName());

        $descriptor->setManager($this);

        $this->descriptors[$key] = $descriptor;
    }

    /**
     * @param string              $alias
     * @param DescriptorInterface $prototype
     * @param array               $params
     * @param array               $actions
     *
     * @return Descriptor|DescriptorInterface
     */
    public function alias($alias, DescriptorInterface $prototype, $params = null, $actions = null)
    {
        $descriptor = clone $prototype;

        $descriptor->setParams($params)->setActions($actions)->setName($alias);

        $key                     = $this->makeKey($alias);
        $this->descriptors[$key] = $descriptor;

        return $descriptor;
    }

    /**
     * @param string          $className
     * @param array           $params
     * @param array           $actions
     * @param ReflectionClass $reflectionClass
     * @param string          $implementation
     *
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     * @return Descriptor|DescriptorInterface
     */
    public function describe(
        $className,
        $params = null,
        $actions = null,
        ReflectionClass $reflectionClass = null,
        $implementation = null
    ) {
        $key = $this->makeKey($className);

        if (isset($this->descriptors[$key])) {
            $descriptor = $this->descriptors[$key];
            $descriptor->setParams($params);

            if (!is_null($actions)) {
                $descriptor->setActions($actions);
            }

            return $descriptor;
        }

        if (!class_exists($className) && !interface_exists($className)) {
            throw new InvalidArgumentException("Class '$className' could not be found");
        }

        if (!is_null($implementation)) {
            if (!class_exists($implementation) && !interface_exists($implementation)) {
                throw new InvalidArgumentException("Class '$implementation' could not be found");
            }

            $reflectionClass = new ReflectionClass($implementation);

            if (!$reflectionClass->isSubclassOf($className)) {
                throw new InvalidArgumentException("Given class '$implementation' does not inherit from '$className'");
            }
        }

        if (!($reflectionClass instanceof ReflectionClass)) {
            $reflectionClass = new ReflectionClass($className);
        }

        $descriptor              = clone $this->descriptorPrototype;
        $this->descriptors[$key] = $descriptor;

        $descriptor->load($reflectionClass)->setParams($params)->setActions($actions);

        return $descriptor;
    }

    /**
     * @param string $name Class name or alias
     * @param array  $paramsOverride
     *
     * @return object
     * @throws \RuntimeException
     */
    public function get($name, $paramsOverride = array())
    {
        $key             = $this->makeKey($name);
        $descriptor      = $this->describe($name);
        $reflectionClass = $descriptor->getReflectionClass();
        $class           = $reflectionClass->getName();

        if (in_array($class, $this->queue)) {
            $parent = end($this->queue);

            throw new CircularReferenceException("Circular dependency found for class '$class' in class '$parent'. Please use a setter method to resolve this.");
        }

        array_push($this->queue, $class);

        $params = $descriptor->getParams();

        if (!empty($paramsOverride)) {
            $paramsKeys     = array_map(array($descriptor, 'resolveParamName'), array_keys($paramsOverride));
            $paramsOverride = array_combine($paramsKeys, $paramsOverride);
            $params         = array_replace($params, $paramsOverride);
        }

        $args = $this->resolveParams($params);

        array_pop($this->queue);

        if ($descriptor->isShared() && isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if ($descriptor->isCloneable() && isset($this->instances[$key])) {
            return clone $this->instances[$key];
        }

        if (!$reflectionClass->isInstantiable()) {
            throw new RuntimeException("Class '$class' is is not instantiable");
        }

        $this->instances[$key] = null;

        if (empty($args)) {
            $instance = $reflectionClass->newInstance();
        }
        else {
            $instance = $reflectionClass->newInstanceArgs($args);
        }

        $this->instances[$key] = $instance;

        $this->executeActions($descriptor->getActions(), $this->instances[$key]);

        return $instance;
    }

    /**
     * @param string $interface
     * @param string $name
     * @param array  $actions
     *
     * @return Descriptor|DescriptorInterface
     */
    public function implement($interface, $name, $actions = null)
    {
        return $this->describe($interface, null, $actions, null, $name);
    }

    /**
     * Resolve an array of mixed parameters and possible Descriptors.
     *
     * @param array $params
     *
     * @return array
     */
    public function resolveParams($params)
    {
        $resolved = array();

        foreach ($params as $param) {
            if ($param instanceof DescriptorInterface) {
                $resolved[] = $this->get($param->getName());

                continue;
            }

            $resolved[] = $param;
        }

        return $resolved;
    }

    /**
     * Store an instance for injection by class name or alias.
     *
     * @param string $name Class name or alias
     * @param object $instance
     *
     * @return Manager
     */
    public function set($name, $instance)
    {
        $key = $this->makeKey($name);

        $this->alias($name, $this->describe(get_class($instance)));

        $this->instances[$key] = $instance;

        return $this;
    }

    /**
     * @param array  $actions
     * @param object $instance
     */
    protected function executeActions($actions, $instance)
    {
        if (!is_array($actions) || empty($actions)) {
            return;
        }

        foreach ($actions as $action) {
            try {
                if ($action instanceof InjectorInterface) {
                    $action->setParams($this->resolveParams($action->getParams()));
                }

                $action->execute($instance);
            }
            catch (CircularReferenceException $e) {
                $this->deferredActions->attach(new DeferredAction($action, $instance));
            }
        }

        if ($this->deferredActions->count() > 0) {
            /** @var $deferredAction DeferredAction */
            foreach ($this->deferredActions as $deferredAction) {
                $action = $deferredAction->getAction();

                try {
                    if ($action instanceof InjectorInterface) {
                        $action->setParams($this->resolveParams($action->getParams()));
                    }

                    $action->execute($deferredAction->getContext());

                    $this->deferredActions->detach($deferredAction);
                }
                catch (CircularReferenceException $e) {
                }
            }
        }
    }

    /**
     * @param string $name Class name or alias
     *
     * @return string
     */
    protected function makeKey($name)
    {
        return trim(strtolower($name), '\\');
    }
}
