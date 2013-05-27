<?php

namespace Depend\Abstraction;

interface ActionInterface
{
    /**
     * @param object $object
     *
     * @return mixed
     */
    public function execute($object);
}
