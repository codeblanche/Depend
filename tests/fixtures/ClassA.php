<?php

use Depend\Exception\InvalidArgumentException;

class ClassA
{
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
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $array;

    /**
     * @var Depend\Exception\InvalidArgumentException
     */
    protected $except;

    /**
     * @param ClassB                                         $b
     * @param ClassC                                         $c
     * @param ClassD                                         $d
     * @param string                                         $name
     * @param array                                          $array
     * @param Depend\Exception\InvalidArgumentException      $except
     */
    function __construct(
        ClassB $b,
        ClassC $c,
        ClassD $d,
        $name,
        $array,
        InvalidArgumentException $except
    ) {
        $this->b      = $b;
        $this->c      = $c;
        $this->d      = $d;
        $this->name   = $name;
        $this->except = $except;
        $this->array  = $array;
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
}
