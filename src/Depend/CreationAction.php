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

    /**
     * Returns an unique identifier for the function/method
     *
     * @return string
     */
    public function getIdentifier()
    {
        if (is_array($this->callback)) {
            $class = $this->callback[0];

            if (is_object($class)) {
                $class = get_class($class);
            }

            return $class . '::' . $this->callback[1];
        }
        else if (is_object($this->callback)) {
            return spl_object_hash((object) $this->callback);
        }

        return (string) $this->callback;
    }
}
