<?php

class ClassF
{
    const TEST_CONST = 'test_const';

    /**
     * @var string
     */
    protected $value1;

    /**
     * @var mixed
     */
    protected $value2;

    /**
     * @var array
     */
    protected $value3;

    /**
     * @var ClassE
     */
    protected $value4;

    /**
     * Default constructor
     *
     * @param string $value1
     * @param string $value2
     * @param array  $value3
     * @param ClassE $value4
     */
    function __construct($value1 = 'someValue', $value2 = self::TEST_CONST, array $value3 = array(), ClassE $value4 = null)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->value3 = $value3;
        $this->value4 = $value4;
    }
}
