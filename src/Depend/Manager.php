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
     * @param string $className
     *
     * @return object
     */
    public function instance($className)
    {
        return $this->get($className);
    }

    /**
     * @param string $className
     *
     * @return object
     */
    public function get($className)
    {
        $descriptor = $this->describe($className);

        return $this->factory->create($descriptor);
    }

    /**
     * @param string          $className
     * @param array           $params
     * @param ReflectionClass $reflectionClass
     *
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     * @return DescriptorInterface
     */
    public function describe($className, $params = null, ReflectionClass $reflectionClass = null)
    {
        if (!class_exists($className) && !interface_exists($className)) {
            throw new InvalidArgumentException("Class '$className' could not be found");
        }

        $key = $this->makeKey($className);

        if (isset($this->descriptors[$key])) {
            return $this->descriptors[$key]->setParams($params);
        }

        if (!($reflectionClass instanceof ReflectionClass)) {
            $reflectionClass = new ReflectionClass($className);
        }

        if ($reflectionClass->isInterface()) {
            throw new RuntimeException("Given class name '$className' is an interface.\nPlease use the 'Manager::implement({interfaceName}, {className})' method to describe " . "your implementation class.");
        }

        $descriptor = clone $this->descriptorPrototype;

        $this->descriptors[$key] = $descriptor;

        $descriptor->load($reflectionClass)->setParams($params);

        return $descriptor;
    }

    /**
     * @param string $className
     *
     * @return string
     */
    protected function makeKey($className)
    {
        return trim(strtolower($className), '\\');
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
     * @param string $class
     *
     * @throws Exception\InvalidArgumentException
     * @return DescriptorInterface
     */
    public function implement($interface, $class)
    {
        $descriptor = $this->describe($class);

        if (!$descriptor->getReflectionClass()->implementsInterface($interface)) {
            throw new InvalidArgumentException("Given class '$class' does not implement '$interface'");
        }

        $key                     = $this->makeKey($interface);
        $this->descriptors[$key] = $descriptor;

        return $descriptor;
    }
}
