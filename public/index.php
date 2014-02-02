<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../fixtures/InterfaceOne.php';
require_once __DIR__ . '/../fixtures/ClassA.php';
require_once __DIR__ . '/../fixtures/ClassB.php';
require_once __DIR__ . '/../fixtures/ClassC.php';
require_once __DIR__ . '/../fixtures/ClassD.php';
require_once __DIR__ . '/../fixtures/ClassE.php';
require_once __DIR__ . '/../fixtures/ClassF.php';
require_once __DIR__ . '/../fixtures/ClassOne.php';
require_once __DIR__ . '/../fixtures/ClassXA.php';
require_once __DIR__ . '/../fixtures/ClassCircularRefA.php';
require_once __DIR__ . '/../fixtures/ClassCircularRefB.php';
require_once __DIR__ . '/../fixtures/ClassNoInstance.php';
require_once __DIR__ . '/../fixtures/ClassStub.php';

ini_set('xdebug.collect_params', 4);
ini_set('xdebug.var_display_max_depth', 10);

/*
 * Manager throws exceptions so we can catch them.
 *
 * - \Manager\Exception\InvalidArgumentException
 * - \Manager\Exception\RuntimeException
 */
try {

    /*
     * Start by creating the Dependency Manager with an
     * optional Factory dependency override.
     *
     * (1) Factory    $factory
     *
     * When the factory is not provided Manager will use
     * it's own internal implementation.
     */
    $dm = new \Depend\Manager();

    /*
     * Create an InjectorFactory if you need to inject
     * dependencies using setters.
     *
     * For advanced developers you can override the injector
     * prototype by providing an instance of your own
     * \Manager\Abstraction\InjectorInterface implementation
     * as the first and only optional argument.
     */
    $if = new \Depend\InjectorFactory();

    /*
     * Tell the manager that any dependencies on InterfaceOne
     * should receive an instance of ClassOne.
     *
     * If your class does not implement the interface you can
     * expect an \Manager\Exception\InvalidArgumentException.
     */
    $dm->implement('InterfaceOne', 'ClassOne');

    /*
     * Some classes may need specific arguments to provided to
     * the constructor or make use of setter methods to inject
     * it's dependencies.
     *
     * To facilitate this Manager makes use of Descriptors. You
     * can use the Managers 'describe' method to describe the
     * additional requirements of a class. If there are no specific
     * requirements Manager will automatically be able to link up
     * the dependencies by reflecting the class in question.
     *
     * \Manager\Manager::describe takes the following arguments;
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
     * or by index 0 => 'paramValue'. You may also provide a \Manager\ClassDescriptor
     * returned by the describe method as a paramValue so that it can be
     * resolved at the time of creating your class.
     *
     * $actions must be an array containing only InjectorInterface
     * implementations. Other objects/values will be ignored.
     *
     * $reflectionClass is used internally by Manager for optimization
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

    $e = $dm->describe('ClassE')->setActions(
        array(
            $if->create('setInterfaceOne', $dm->describe('InterfaceOne')),
        )
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

    /*
     * Get an instance of the class you want. You can also provide a second
     * optional parameter array containing arguments that should override
     * any previously described arguments.
     */
    $a = $dm->get('ClassA');

    var_dump($a);
}
catch (Exception $e) {
    $trace = $e->getTrace();

    echo '<pre>' . $e->getMessage() . ' at ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'] . "\n";
    echo '-----------------------------------' . "\n";
    echo $e->getTraceAsString();
}


