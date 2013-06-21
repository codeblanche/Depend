<?php


class ClassCircularRefA 
{
    /**
     * @var ClassCircularRefB
     */
    protected $b;

    function __construct(ClassCircularRefB $b)
    {
        $this->b = $b;
    }
}
