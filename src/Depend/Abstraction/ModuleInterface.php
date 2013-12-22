<?php

namespace Depend\Abstraction;

use Depend\Manager;

interface ModuleInterface
{
    /**
     * Register the modules classes and interfaces with Depend\Manager
     *
     * @param Manager $dm
     *
     * @return void
     */
    public function register(Manager $dm);
}
