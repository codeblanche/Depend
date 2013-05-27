<?php

namespace Depend\Abstraction;

use Depend\Manager;
use ReflectionClass;

interface DescriptorInterface
{
    /**
     * @param ReflectionClass $class
     *
     * @return DescriptorInterface
     */
    public function load(ReflectionClass $class);

    /**
     * @return boolean
     */
    public function isShared();

    /**
     * @return boolean
     */
    public function isCloneable();

    /**
     * @param array $value
     *
     * @return DescriptorInterface
     */
    public function setParams($value);

    /**
     * @return array
     */
    public function getParams();

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass();

    /**
     * @param string $name
     *
     * @return DescriptorInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param Manager $manager
     *
     * @return DescriptorInterface
     */
    public function setManager(Manager $manager);

    /**
     * @param ActionInterface[] $actions
     *
     * @return DescriptorInterface
     */
    public function setActions($actions);

    /**
     * @return ActionInterface[]
     */
    public function getActions();

    /**
     * @param string $name
     * @param array  $params
     * @param array  $actions
     *
     * @return DescriptorInterface
     */
    public function alias($name, $params = null, $actions = null);
}
