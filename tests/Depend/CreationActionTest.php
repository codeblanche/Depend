<?php

namespace Depend;

class CreationActionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CreationAction
     */
    protected $action;

    public function setUp()
    {
        $this->action = new CreationAction();
    }

    public function testExecute()
    {
        $called      = false;
        $objectParam = null;

        $this->action->setCallback(
            function ($object) use (&$called, &$objectParam) {
                $called      = true;
                $objectParam = $object;
            }
        );

        $objectInput = (object) array(1, 2, 3);

        $this->action->execute($objectInput);

        $this->assertTrue($called);
        $this->assertEquals($objectInput, $objectParam);
        $this->assertEquals(spl_object_hash($objectInput), spl_object_hash($objectParam));
    }

    public function testExecuteGracefulFailure()
    {
        $this->action->setCallback(null);

        $this->action->execute((object) array(1, 2, 3));

        $this->assertNull($this->action->getCallback());
    }

    public function testSetGetCallback()
    {
        $callback = function ($object) {
            // do nothing
        };

        $returned = $this->action->setCallback($callback);

        $this->assertEquals($callback, $this->action->getCallback());
        $this->assertInstanceOf('\Depend\CreationAction', $returned);
        $this->assertEquals(spl_object_hash($this->action), spl_object_hash($returned));
    }
}
