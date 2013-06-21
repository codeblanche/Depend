<?php

use Depend\Exception\InvalidArgumentException;
use Depend\Manager;

class ClassA
{
    /**
     * @var array
     */
    protected $array;

    /**
     * @var ClassB
     */
    protected $b;

    /**
     * @var ClassC
     */
    protected $c;

    /**
     * @var ClassD
     */
    protected $d;

    /**
     * @param ClassC         $c
     * @param ClassD         $d
     * @param string         $name
     * @param array          $array
     */
    function __construct(
        ClassC $c,
        ClassD $d,
        $name,
        $array
    ) {
        $this->c       = $c;
        $this->d       = $d;
        $this->name    = $name;
        $this->array   = $array;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @return ClassB
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return ClassC
     */
    public function getC()
    {
        return $this->c;
    }

    /**
     * @return ClassD
     */
    public function getD()
    {
        return $this->d;
    }

    /**
     * @return Depend\Exception\InvalidArgumentException
     */
    public function getExcept()
    {
        return $this->except;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ClassB $b
     */
    public function setB($b)
    {
        $this->b = $b;
    }
}
