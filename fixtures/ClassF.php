<?php

class ClassF
{
    const TEST_CONST = 'test_const';

    /**
     * @var string
     */
    protected $param1;

    /**
     * @var mixed
     */
    protected $param2;

    /**
     * @var array
     */
    protected $param3;

    /**
     * @var ClassE
     */
    protected $param4;

    /**
     * Default constructor
     *
     * @param string $param1
     * @param string $param2
     * @param array  $param3
     * @param ClassE $param4
     */
    function __construct($param1 = 'someValue', $param2 = self::TEST_CONST, array $param3 = array(), ClassE $param4 = null)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->param4 = $param4;
    }

    /**
     * @return string
     */
    public function getParam1()
    {
        return $this->param1;
    }

    /**
     * @return mixed
     */
    public function getParam2()
    {
        return $this->param2;
    }

    /**
     * @return array
     */
    public function getParam3()
    {
        return $this->param3;
    }

    /**
     * @return \ClassE
     */
    public function getParam4()
    {
        return $this->param4;
    }


}
