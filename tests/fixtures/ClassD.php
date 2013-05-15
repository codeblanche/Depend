<?php

class ClassD
{
    /**
     * @var InterfaceOne
     */
    protected $interfaceOne;

    /**
     * @param InterfaceOne $interfaceOne
     */
    function __construct(InterfaceOne $interfaceOne)
    {
        $this->interfaceOne = $interfaceOne;
    }

    /**
     * @return InterfaceOne
     */
    public function getInterfaceOne()
    {
        return $this->interfaceOne;
    }
}
