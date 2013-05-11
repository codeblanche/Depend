<?php

namespace DI;

use DI\Abstraction\DescriptorInterface;
use DI\Abstraction\FactoryInterface;
use DI\Exception\InvalidArgumentException;

class Factory implements FactoryInterface
{
    /**
     * @var object[]
     */
    protected $instances = array();

    /**
     * @param DescriptorInterface $descriptor
     *
     * @return object
     */
    public function create(DescriptorInterface $descriptor)
    {
        if ($descriptor->isShared()) {
            return $this->get($descriptor);
        }

        if ($descriptor->isCloneable()) {
            return clone $this->get($descriptor);
        }

        return $this->get($descriptor, true);
    }

    /**
     * @param DescriptorInterface $descriptor
     * @param bool                $new
     *
     * @return object
     */
    protected function get(DescriptorInterface $descriptor, $new = false)
    {
        $reflectionClass = $descriptor->getReflectionClass();
        $class           = $reflectionClass->getName();
        $params          = $descriptor->getParams();

        if (!isset($this->instances[$class]) || $new === true) {
            $this->instances[$class] = $reflectionClass->newInstanceArgs(
                $this->resolveDescriptors($params)
            );
        }

        // TODO: check for callbacks and execute them - also resolveDescriptors for callback parameters.

        return $this->instances[$class];
    }

    /**
     * @param array $params
     *
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    protected function resolveDescriptors($params)
    {
        if (!is_array($params)) {
            throw new InvalidArgumentException('Expected an array.');
        }

        foreach ($params as &$param) {
            if ($param instanceof DescriptorInterface) {
                $param = $this->create($param);
            }
        }

        return $params;
    }
}
