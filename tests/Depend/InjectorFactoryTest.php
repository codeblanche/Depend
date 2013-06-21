<?php

namespace Depend;

class InjectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InjectorFactory
     */
    protected $obj;

    public function setUp()
    {
        $this->obj = new InjectorFactory();
    }

    public function testCreate()
    {
        $injector = $this->obj->create('setDependency', array('dependency'));

        $this->assertInstanceOf('\Depend\Abstraction\InjectorInterface', $injector);
    }
}
