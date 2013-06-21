<?php

class ClassB
{
    /**
     * @var ClassA
     */
    protected $a;

    /**
     * @var ClassC
     */
    protected $c;

    /**
     * @var ClassD
     */
    protected $d;

    /**
     * @var ClassE
     */
    protected $e;

    /**
     * @param ClassC $c
     * @param ClassD $d
     * @param ClassE $e
     */
    function __construct(ClassC $c, ClassD $d, ClassE $e)
    {
        $this->c = $c;
        $this->d = $d;
        $this->e = $e;
    }

    /**
     * @param ClassA $a
     */
    public function setA($a)
    {
        $this->a = $a;
    }

    /**
     * @return ClassA
     */
    public function getA()
    {
        return $this->a;
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
     * @return ClassE
     */
    public function getE()
    {
        return $this->e;
    }
}
