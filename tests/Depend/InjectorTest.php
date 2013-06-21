<?php

namespace Depend;

use Depend\Exception\RuntimeException;

class InjectorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Injector
     */
    protected $injector;

    protected $methodSet = false;

    public function callbackExecute($param1, $param2, $param3)
    {
        $this->assertEquals(1, $param1);
        $this->assertEquals(2, $param2);
        $this->assertEquals(3, $param3);
    }

    public function callbackSetMethodName()
    {
        $this->methodSet = true;
    }

    public function setUp()
    {
        $this->injector = new Injector();
    }

    public function testExecute()
    {
        $this->injector->setMethodName('callbackExecute');

        $params = array(1, 2, 3);

        $this->injector->setParams($params);

        $this->injector->execute($this);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExecuteFailure()
    {
        $this->injector->setMethodName('nonExistentMethod');

        $this->injector->execute($this);
    }

    public function testSetGetParams()
    {
        $params = array(1, 2, 3);

        $this->injector->setParams($params);

        $this->assertEquals($params, $this->injector->getParams());
    }

    public function testSetMethodName()
    {
        $this->injector->setMethodName('callbackSetMethodName');

        $this->injector->execute($this);

        $this->assertTrue($this->methodSet);
    }


}
