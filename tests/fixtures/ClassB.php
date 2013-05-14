<?php

class ClassB
{
    protected $a;

    protected $c;

    protected $d;

    protected $e;

    function __construct(ClassA $a, ClassC $c, ClassD $d, ClassE $e)
    {
        $this->a = $a;
        $this->c = $c;
        $this->d = $d;
        $this->e = $e;
    }
}
