<?php

namespace Depend\Abstraction;

interface FactoryInterface
{
    /**
     * @param DescriptorInterface $descriptor
     *
     * @return object
     */
    public function create(DescriptorInterface $descriptor);
}
