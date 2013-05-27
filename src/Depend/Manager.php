<?php

namespace Depend;

use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\FactoryInterface;
use Depend\Exception\InvalidArgumentException;
use Depend\Exception\RuntimeException;
use ReflectionClass;

class Manager
{
    /**
     * @var DescriptorInterface[]
     */
    protected $descriptors = array();

    /**
     * @var DescriptorInterface[]
     */
    protected $named = array();

    /**
     * @var DescriptorInterface
     */
    protected $descriptorPrototype;

    /**
     * @var FactoryInterface
     */
    protected $factory;

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
    }

    /**
     * Alias for Manager::get($className);
     *
     * @param string $name Class name or alias
     *
     * @return object
     */
    public function instance($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name Class name or alias
     *
     * @return object
     */
    public function get($name)
    {
        $key = $this->makeKey($name);

        if (!isset($this->descriptors[$key])) {
            $this->describe($name);
        }

        return $this->factory->create($this->descriptors[$key]);
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
     *
     * @return string
     */
    protected function makeKey($name)
    {
        return trim(strtolower($name), '\\');
    }

    /**
     * Add a class descriptor to the managers collection.
     *
     * @param DescriptorInterface $descriptor
     */
    public function add(DescriptorInterface $descriptor)
    {
        $key = $this->makeKey($descriptor->getName());

        $descriptor->setManager($this);

        $this->descriptors[$key] = $descriptor;
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
}
