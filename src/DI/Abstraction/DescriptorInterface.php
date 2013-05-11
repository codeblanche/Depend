<?php

namespace DI\Abstraction;

use DI\Manager;
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
    public function param($identifier, $value);

    /**
     * Call a method after instantiation with the specified parameters.
     *
     * @param callable|string $callback
     * @param array           $params
     *
     * @return mixed
     */
    public function call($callback, array $params);
}
