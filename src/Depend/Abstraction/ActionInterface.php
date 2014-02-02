<?php

namespace Depend\Abstraction;

interface ActionInterface
{
    /**
     * Execute the action.
     *
     * @param object $object
     *
     * @return mixed
     */
    public function execute($object);

    /**
     * Returns an unique identifier for the function/method
     *
     * @return string
     */
    public function getIdentifier();
}
