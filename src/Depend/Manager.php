<?php

namespace Depend;

use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\FactoryInterface;
use Depend\Exception\InvalidArgumentException;

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
     * Add a class descriptor to the managers collection.
     *
     * @param DescriptorInterface $descriptor
     */
    public function add(DescriptorInterface $descriptor)
    {
        $key                     = $this->makeKey($descriptor->getName());
        $this->descriptors[$key] = $descriptor;
    }

    /**
     * @param string $interface
     * @param string $class
     *
     * @return $this
     */
    public function alias($interface, $class)
    {
        $descriptor = $this->describe($class);

        // TODO: ensure that class implements specified interface.

        $key                     = $this->makeKey($interface);
        $this->descriptors[$key] = $descriptor;

        return $this;
    }

    /**
     * @param string           $className
     * @param \ReflectionClass $reflectionClass
     *
     * @throws Exception\InvalidArgumentException
     * @return \DI\Abstraction\DescriptorInterface
     */
    public function describe($className, \ReflectionClass $reflectionClass = null)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class '$className' could not be found");
        }

        $key = $this->makeKey($className);

        if (isset($this->descriptors[$key])) {
            return $this->descriptors[$key];
        }

        $descriptor = clone $this->descriptorPrototype;

        $this->descriptors[$key] = $descriptor;

        if (!($reflectionClass instanceof \ReflectionClass)) {
            $reflectionClass = new \ReflectionClass($className);
        }

        $descriptor->load($reflectionClass);

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
}