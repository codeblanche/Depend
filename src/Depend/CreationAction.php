<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;

class CreationAction implements ActionInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param object $object
     *
     * @return void
     */
    public function execute($object)
    {
        if (!is_callable($this->callback)) {
            return;
        }

        call_user_func($this->callback, $object);
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     *
     * @return CreationAction
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }
}
