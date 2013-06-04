<?php

namespace Depend\Abstraction;

use Depend\Manager;

interface FactoryInterface
{
    /**
     * @param DescriptorInterface $descriptor
     * @param Manager             $manager
     *
     * @return object
     */
    public function create(DescriptorInterface $descriptor, Manager $manager);
}
