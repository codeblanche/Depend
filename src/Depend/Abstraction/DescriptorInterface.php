<?php

namespace Depend\Abstraction;

use Depend\Manager;
use ReflectionClass;

interface DescriptorInterface
{
    /**
     * @param string $name
     * @param array  $params
     * @param array  $actions
     *
     * @return DescriptorInterface
     */
    public function alias($name, $params = null, $actions = null);

    /**
     * @return ActionInterface[]
     */
    public function getActions();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getParams();

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass();

    /**
     * @return boolean
     */
    public function isCloneable();

    /**
     * @return boolean
     */
    public function isShared();

    /**
     * @param ReflectionClass $class
     *
     * @return DescriptorInterface
     */
    public function load(ReflectionClass $class);

    /**
     * @param ActionInterface[] $actions
     *
     * @return DescriptorInterface
     */
    public function setActions($actions);

    /**
     * @param boolean $value
     *
     * @return DescriptorInterface
     */
    public function setIsShared($value);

    /**
     * @param Manager $manager
     *
     * @return DescriptorInterface
     */
    public function setManager(Manager $manager);

    /**
     * @param string $name
     *
     * @return DescriptorInterface
     */
    public function setName($name);

    /**
     * @param array $value
     *
     * @return DescriptorInterface
     */
    public function setParams($value);
}
