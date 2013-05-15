<?php

class ClassE
{
    /**
     * @var InterfaceOne
     */
    protected $interfaceOne;

    /**
     * @param InterfaceOne $interfaceOne
     */
    public function setInterfaceOne(InterfaceOne $interfaceOne)
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
