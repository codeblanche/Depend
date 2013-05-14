<?php

class ClassA
{
    /**
     * @var \ClassB
     */
    protected $b;

    /**
     * @var \ClassC
     */
    protected $c;

    /**
     * @var \ClassD
     */
    protected $d;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $except;

    /**
     * @var array
     */
    protected $array;

    /**
     * @param \ClassB                                    $b
     * @param \ClassC                                    $c
     * @param \ClassD                                    $d
     * @param string                                     $name
     * @param array                                      $array
     * @param Depend\Exception\InvalidArgumentException      $except
     */
    function __construct(
        \ClassB $b,
        \ClassC $c,
        \ClassD $d,
        $name,
        $array,
        \Depend\Exception\InvalidArgumentException $except
    ) {
        $this->setClassB($b);
        $this->setClassC($c);
        $this->setClassD($d);
        $this->setName($name);
        $this->except = $except;
        $this->array  = $array;
    }

    /**
     * @param \ClassB $b
     */
    public function setClassB($b)
    {
        $this->b = $b;
    }

    /**
     * @param \ClassC $c
     */
    public function setClassC($c)
    {
        $this->c = $c;
    }

    /**
     * @param \ClassD $d
     */
    public function setClassD($d)
    {
        $this->d = $d;
    }

    /**
     * @return \ClassB
     */
    public function getClassB()
    {
        return $this->b;
    }

    /**
     * @return \ClassC
     */
    public function getClassC()
    {
        return $this->c;
    }

    /**
     * @return \ClassD
     */
    public function getClassD()
    {
        return $this->d;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
