<?php

namespace DI;

use DI\Abstraction\DescriptorInterface;
use DI\Abstraction\FactoryInterface;
use DI\Exception\InvalidArgumentException;

class Manager
{
    /**
     * @var DescriptorInterface[]
     */
    protected $descriptors;

    /**
     * @var DescriptorInterface[]
     */
    protected $named;

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
     * @param string $className
     *
     * @throws Exception\InvalidArgumentException
     * @return \DI\Abstraction\DescriptorInterface
     */
    public function describe($className)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class '$className' could not be found");
        }

        $key = $this->makeKey($className);

        if (isset($this->descriptors[$key])) {
            return $this->descriptors[$key];
        }

        $descriptor = clone $this->descriptorPrototype;

        $descriptor->load(new \ReflectionClass($className));

        $this->descriptors[$key] = $descriptor;

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
