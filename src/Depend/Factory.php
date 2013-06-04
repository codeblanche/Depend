<?php

namespace Depend;

use Depend\Abstraction\DescriptorInterface;
use Depend\Abstraction\FactoryInterface;
use Depend\Exception\RuntimeException;

class Factory implements FactoryInterface
{
    /**
     * @param DescriptorInterface $descriptor
     * @param Manager             $manager
     *
     * @throws Exception\RuntimeException
     * @return object
     */
    public function create(DescriptorInterface $descriptor, Manager $manager)
    {
        $reflectionClass = $descriptor->getReflectionClass();
        $class           = $reflectionClass->getName();

        if (!$reflectionClass->isInstantiable()) {
            throw new RuntimeException("Class '$class' is is not instantiable");
        }

        $args     = $manager->resolveParams($descriptor->getParams());
        $instance = $reflectionClass->newInstanceArgs($args);

        return $instance;
    }
}
