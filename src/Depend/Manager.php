<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;
use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\FactoryInterface;
use Depend\Abstraction\InjectorInterface;
use Depend\Exception\InvalidArgumentException;
use Depend\Exception\RuntimeException;
use ReflectionClass;

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
     * @var FactoryInterface
     */
    protected $factory;

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
     * @param FactoryInterface    $factory
     * @param DescriptorInterface $descriptorPrototype
     */
    public function __construct(FactoryInterface $factory = null, DescriptorInterface $descriptorPrototype = null)
    {
        if (!($factory instanceof FactoryInterface)) {
            $factory = new Factory;
        }

        if (!($descriptorPrototype instanceof DescriptorInterface)) {
            $descriptorPrototype = new Descriptor;
        }

        $this->descriptorPrototype = $descriptorPrototype;
        $this->factory             = $factory;

        $this->descriptorPrototype->setManager($this);

        $this->implement('Depend\Abstraction\FactoryInterface', 'Depend\Factory');
        $this->implement('Depend\Abstraction\DescriptorInterface', 'Depend\Descriptor')->setIsShared(false);

        $this->describe('Depend\Manager');

        $this->set('Depend\Manager', $this)->set('Depend\Factory', $factory)->set(
            'Depend\Descriptor',
            $descriptorPrototype
        );
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
     * @return DescriptorInterface
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
     *
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     * @return DescriptorInterface
     */
    public function describe($className, $params = null, $actions = null, ReflectionClass $reflectionClass = null)
    {
        $key = $this->makeKey($className);

        if (isset($this->descriptors[$key])) {
            return $this->descriptors[$key]->setParams($params);
        }

        if (!class_exists($className) && !interface_exists($className)) {
            throw new InvalidArgumentException("Class '$className' could not be found");
        }

        if (!($reflectionClass instanceof ReflectionClass)) {
            $reflectionClass = new ReflectionClass($className);
        }

        if ($reflectionClass->isInterface()) {
            throw new RuntimeException("Given class name '$className' is an interface.\nPlease use the 'Manager::implement({interfaceName}, {className})' method to describe " . "your implementation class.");
        }

        $descriptor = clone $this->descriptorPrototype;

        $this->descriptors[$key] = $descriptor;

        $descriptor->load($reflectionClass)->setParams($params)->setActions($actions);

        return $descriptor;
    }

    /**
     * @param string $name Class name or alias
     * @param array  $paramsOverride
     *
     * @return object
     */
    public function get($name, $paramsOverride = null)
    {
        $descriptor = $this->describe($name);
        $key        = $this->makeKey($name);

        if (is_array($paramsOverride) && !empty($paramsOverride)) {
            $descriptor = clone $descriptor;
            $descriptor->setParams($paramsOverride);

            return $this->create($descriptor);
        }

        if (!isset($this->instances[$key])) {
            $this->create($descriptor, $this->instances[$key]);
        }

        if ($descriptor->isShared()) {
            return $this->instances[$key];
        }

        if ($descriptor->isCloneable()) {
            return clone $this->instances[$key];
        }

        $instance = $this->instances[$key];

        unset($this->instances[$key]);

        return $instance;
    }

    /**
     * @param string $interface
     * @param string $name
     *
     * @throws Exception\InvalidArgumentException
     * @return DescriptorInterface
     */
    public function implement($interface, $name)
    {
        $descriptor = $this->describe($name);

        if (!$descriptor->getReflectionClass()->implementsInterface($interface)) {
            throw new InvalidArgumentException("Given class '$name' does not implement '$interface'");
        }

        $key                     = $this->makeKey($interface);
        $this->descriptors[$key] = $descriptor;

        return $descriptor;
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
     * @return $this
     */
    public function set($name, $instance)
    {
        $key = $this->makeKey($name);

        $this->instances[$key] = $instance;

        return $this;
    }

    /**
     * Create an instance of the given class descriptor
     *
     * @param Abstraction\DescriptorInterface $descriptor
     * @param object $instance
     *
     * @throws Exception\RuntimeException
     * @internal param $store
     * @return object
     */
    protected function create(DescriptorInterface $descriptor, &$instance = null)
    {
        $class = $descriptor->getReflectionClass()->getName();

        if (in_array($class, $this->queue)) {
            $parent = end($this->queue);

            throw new RuntimeException("Circular dependency found for class '$class' in class '$parent'. Please use a setter method to resolve this.");
        }

        array_push($this->queue, $class);

        $instance = $this->factory->create($descriptor, $this);

        array_pop($this->queue);

        $this->executeActions($descriptor->getActions(), $instance);

        return $instance;
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
            if (!($action instanceof ActionInterface)) {
                continue;
            }

            if ($action instanceof InjectorInterface) {
                $action->setParams($this->resolveParams($action->getParams()));
            }

            $action->execute($instance);
        }

        return;
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
