<?php


class ClassCircularRefB 
{
    /**
     * @var ClassCircularRefA
     */
    protected $a;

    function __construct(ClassCircularRefA $a)
    {
        $this->a = $a;
    }
}
