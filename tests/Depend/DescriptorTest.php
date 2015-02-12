<?php

namespace Depend;

use Depend\Exception\RuntimeException;
use ReflectionClass;

class DescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Descriptor
     */
    protected $descriptor;

    public function setUp()
    {
        $this->descriptor = new Descriptor();
    }

    public function testSetGetManager()
    {
        $manager = new Manager();

        $this->descriptor->setManager($manager);

        $this->assertEquals(
            spl_object_hash($manager),
            spl_object_hash($this->descriptor->getManager())
        );
    }

    public function testSetGetIsCloneable()
    {
        $this->descriptor->setIsCloneable(false);

        $this->assertFalse($this->descriptor->isCloneable());

        $this->descriptor->setIsCloneable(true);

        $this->assertTrue($this->descriptor->isCloneable());
    }

    public function testSetGetIsShared()
    {
        $this->descriptor->setIsShared(false);

        $this->assertFalse($this->descriptor->isShared());

        $this->descriptor->setIsShared(true);

        $this->assertTrue($this->descriptor->isShared());
    }

    public function testSetGetParams()
    {
        $params = array(1, 2, 3);

        $this->descriptor->setParams($params);

        $this->assertEquals($params, $this->descriptor->getParams());
    }

    public function getReflectionClass()
    {
        $descriptor = clone $this->descriptor;

        $reflectionClass = new ReflectionClass('ClassC');

        $descriptor->load($reflectionClass);

        $this->assertInstanceOf('\ReflectionClass', $descriptor->getReflectionClass());
        $this->assertEquals(
            spl_object_hash($reflectionClass),
            spl_object_hash($descriptor->getReflectionClass())
        );
    }

    public function testLoad()
    {
        $manager = new Manager();

        $manager->implement('InterfaceOne', 'ClassOne');

        $this->descriptor->setManager($manager);

        $result = $this->descriptor->load(new ReflectionClass('ClassA'));

        $this->assertEquals($this->descriptor, $result);
    }

    public function testLoadNoParams()
    {
        $result = $this->descriptor->load(new ReflectionClass('ClassStub'));

        $this->assertEquals($this->descriptor, $result);

        $this->assertEmpty($this->descriptor->getParams());
    }

    public function testLoadNonInstantiable()
    {
        $result = $this->descriptor->load(new ReflectionClass('ClassNoInstance'));

        $this->assertEquals($this->descriptor, $result);
    }

    public function testAddAction()
    {
        $action = new CreationAction();
        $action->setCallback(function ($object) {});

        $this->descriptor->addAction($action);

        $this->assertContains($action, $this->descriptor->getActions());
    }

    public function testSetGetActions()
    {
        $action1 = new CreationAction();
        $action2 = new Injector();

        $action1->setCallback(function ($object) { var_dump($object); });
        $action2->setMethodName('amethod');

        $actions = array(
            $action1,
            $action2,
        );

        $this->descriptor->setActions($actions);

        $this->assertEquals(
            array(
                $action1,
                $action2,
            ),
            $this->descriptor->getActions()
        );
    }

    public function testSetGetName()
    {
        $name = 'testing';

        $this->descriptor->setName($name);

        $this->assertEquals($name, $this->descriptor->getName());
    }

    public function testAlias()
    {
        $manager = new Manager();

        $this->descriptor->setManager($manager);

        $this->descriptor->load(new ReflectionClass('\ClassC'));

        $this->descriptor->alias('descriptorAlias');

        $this->assertEquals(
            spl_object_hash($this->descriptor->getReflectionClass()),
            spl_object_hash($manager->describe('descriptorAlias')->getReflectionClass())
        );
    }

    /**
     * @expectedException RuntimeException
     */
    public function testResolveArgumentValueException()
    {
        $reflectionClass = new ReflectionClass('ClassA');
        $this->descriptor->load($reflectionClass);
    }
}
