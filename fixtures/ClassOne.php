<?php

class ClassOne implements InterfaceOne
{
    /**
     * @var ClassStub
     */
    protected $stub;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @param ClassStub $stub
     *
     * @return $this
     */
    public function setStub(ClassStub $stub)
    {
        $this->stub = $stub;
    }
}
