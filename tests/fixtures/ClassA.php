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
     * @param \ClassB      $b
     * @param \ClassC      $c
     * @param \ClassD      $d
     * @param string $name
     */
    function __construct(\ClassB $b, \ClassC $c, \ClassD $d, $name, $array, \DI\Exception\InvalidArgumentException $except)
    {
        $this->setClassB($b);
        $this->setClassC($c);
        $this->setClassD($d);
        $this->setName($name);
        $this->except = $except;
    }

    /**
     * @return \ClassB
     */
    public function getClassB()
    {
        return $this->b;
    }

    /**
     * @param \ClassB $b
     */
    public function setClassB($b)
    {
        $this->b = $b;
    }

    /**
     * @return \ClassC
     */
    public function getClassC()
    {
        return $this->c;
    }

    /**
     * @param \ClassC $c
     */
    public function setClassC($c)
    {
        $this->c = $c;
    }

    /**
     * @return \ClassD
     */
    public function getClassD()
    {
        return $this->d;
    }

    /**
     * @param \ClassD $d
     */
    public function setClassD($d)
    {
        $this->d = $d;
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
