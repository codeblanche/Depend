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
    public function setParams(array $value);

    /**
     * @return array
     */
    public function getParams();

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass();

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
     * Set one of the constructor parameters by identifier (index or name).
     *
     * @param int|string $identifier
     * @param mixed      $value
     *
     * @return DescriptorInterface
     */
    public function setParam($identifier, $value);

    /**
     * Execute the given callback after class instance is created.
     *
     * @param ActionInterface $action
     *
     * @return DescriptorInterface
     */
    public function addAction(ActionInterface $action);
}
