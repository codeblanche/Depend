<?php

namespace Depend;

use Depend\Abstraction\ActionInterface;

class DeferredAction
{
    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var object
     */
    protected $context;

    /**
     * Default constructor
     *
     * @param ActionInterface $action
     * @param object          $context
     */
    function __construct(ActionInterface $action, $context)
    {
        $this->action  = $action;
        $this->context = $context;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return object
     */
    public function getContext()
    {
        return $this->context;
    }
}
