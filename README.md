# Depend 1.0.3

Less Configuration, More Injection

## Introduction

Depend attempts to make it simpler and easier for developers to take advantage of IOC by automating as much of the process as possible.

The only coding requirement is that your dependencies are clearly defined in the constructors of the classes you are writing. For example;

```php
/*
 * Good
 */
class ClassA
{
    function __construct(ClassC $c, ClassD $d, ClassF $f) 
    {
    }
}

/*
 * Bad
 */
class ClassA
{
    function __construct($c, $d, $f) 
    {
    }
}


```

## Requirements

This project has been setup to be PHP 5.3.3 compatible and composer friendly.

## Installation

Add `"codeblanche/entity": "1.*"` to the `require` section of your projects composer.json file.

## Simplest usage possible

```php
<?php

/*
 * Create an instance of the Manager.
 */
$dm  = new \Depend\Manager();

/*
 * Get an instance of your application class with simple 
 * dependencies automatically resolved.
 */
$app = $dm->get('MyApplication');

```

## A little more advanced usage

See [fixtures](https://github.com/CodeBlanche/Depend/tree/master/fixtures) for the classes being used in this example.

```php
<?php

/*
 * Depend throws exceptions so we can catch them.
 *
 * - \Depend\Exception\InvalidArgumentException
 * - \Depend\Exception\RuntimeException
 */
try {

    /*
     * Start by creating the Manager with two optional parameters.
     *
     * (1) FactoryInterface    $factory
     * (2) DescriptorInterface $descriptorPrototype
     *
     * When the parameters are not provided Depend will use
     * it's own internal implementations.
     */
    $dm = new \Depend\Manager();

    /*
     * Create an InjectorFactory if you need to inject
     * dependencies using setters.
     *
     * For advanced developers you can override the injector
     * prototype by providing an instance of your own
     * \Depend\Abstraction\InjectorInterface implementation
     * as the first and only optional argument.
     */
    /** @var $if \Depend\InjectorFactory */
    $if = $dm->get('\Depend\InjectorFactory');

    /*
     * Load a module that implements \Depend\Abstraction\ModuleInterface
     * to defer configuration of classes and implementation to independent
     * modules.
     *
     * Our recommendation is to include a /Depend/Module.php within your module
     * root.
     *
     * Note: Module order does matter. Modules loaded later may override
     * implementation and definitions created in prior loaded modules.
     */
    $dm->module('\My\Own\Custom\Module\Depend\Module');

    /*
     * Tell the manager that any dependencies on InterfaceOne
     * should receive an instance of ClassOne.
     *
     * If your class does not implement the interface you can
     * expect an \Depend\Exception\InvalidArgumentException.
     */
    $dm->implement('InterfaceOne', 'ClassOne');

    /*
     * Some classes may need specific arguments to provided to
     * the constructor or make use of setter methods to inject
     * it's dependencies.
     *
     * To facilitate this Depend makes use of Descriptors. You
     * can use the Managers 'describe' method to describe the
     * additional requirements of a class. If there are no specific
     * requirements Depend will automatically be able to link up
     * the dependencies by reflecting the class in question.
     *
     * \Depend\Manager::describe takes the following arguments;
     *
     * (1) string                   $className
     * (2) array<mixed>             $params [optional]
     * (3) array<InjectorInterface> $actions [optional]
     * (4) ReflectionClass          $reflectionClass [optional]
     *
     * $className must be a fully qualified class name including
     * it's namespace.
     *
     * $params can be specified by name using 'paramName' => 'paramValue'
     * or by index 0 => 'paramValue'. You may also provide a \Depend\Descriptor
     * returned by the describe method as a paramValue so that it can be
     * resolved at the time of creating your class.
     *
     * $actions must be an array containing only InjectorInterface
     * implementations. Other objects/values will be ignored.
     *
     * $reflectionClass is used internally by Depend for optimization
     * purposes to avoid recreating ReflectionClass objects.
     */
    $dm->describe(
        'ClassA',
        array(
            'name'   => 'test',
            'array'  => array(1, 2, 3),
            'except' => null,
        ),
        array(
            $if->create('setB', $dm->describe('ClassB'))
        )
    );

    /*
     * You can adjust the parameters on your descriptor after is has
     * been created. Multiple calls to 'describe' will not override
     * your descriptors but give you access to the existing one.
     */
    $dm->describe('ClassA')->setParam('name', 'test2');

    /*
     * And add actions on your descriptor after it has been created.
     */
    $dm->describe('ClassB')->addAction(
        $if->create('setA', $dm->describe('ClassA'))
    );

    /*
     * Or reset the entire list of actions by providing a new one.
     */
    $dm->describe('ClassC')->setActions(
        array(
            $if->create('setClassD', $dm->describe('ClassD')),
            $if->create('setClassE', $dm->describe('ClassE')),
            $if->create('setClassOne', $dm->describe('ClassOne')),
            $if->create('setClassXA', $dm->describe('ClassXA')),
        )
    );
    $dm->describe('ClassE')->setActions(
        array(
            $if->create('setInterfaceOne', $dm->describe('InterfaceOne')),
        )
    );
    
    /*
     * Get an instance of the class you want. You can also provide a second
     * optional parameter array containing arguments that should override
     * any previously described arguments.
     */
    $a = $dm->get('ClassA');
}
catch (Exception $e) {
    echo $e->getMessage();
}

```

## License (BSD 3-Clause)

Copyright (c) 2013, CodeBlanche
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this list of conditions and the following
  disclaimer.

- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following
  disclaimer in the documentation and/or other materials provided with the distribution.

- Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or promote products
  derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


