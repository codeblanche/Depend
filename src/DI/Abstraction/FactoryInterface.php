<?php

namespace DI\Abstraction;

interface FactoryInterface
{
    /**
     * @param DescriptorInterface $descriptor
     *
     * @return object
     */
    public function create(DescriptorInterface $descriptor);
}
