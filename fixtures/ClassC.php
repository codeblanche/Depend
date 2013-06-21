<?php

class ClassC
{
    /**
     * @var ClassD
     */
    protected $classD;

    /**
     * @var ClassE
     */
    protected $classE;

    /**
     * @var ClassOne
     */
    protected $classOne;

    /**
     * @var ClassXA
     */
    protected $classXA;

    /**
     * @param ClassD $classD
     */
    public function setClassD(ClassD $classD)
    {
        $this->classD = $classD;
    }

    /**
     * @return ClassD
     */
    public function getClassD()
    {
        return $this->classD;
    }

    /**
     * @param ClassE $classE
     */
    public function setClassE(ClassE $classE)
    {
        $this->classE = $classE;
    }

    /**
     * @return ClassE
     */
    public function getClassE()
    {
        return $this->classE;
    }

    /**
     * @param ClassOne $classOne
     */
    public function setClassOne(ClassOne $classOne)
    {
        $this->classOne = $classOne;
    }

    /**
     * @return ClassOne
     */
    public function getClassOne()
    {
        return $this->classOne;
    }

    /**
     * @param ClassXA $classXA
     */
    public function setClassXA(ClassXA $classXA)
    {
        $this->classXA = $classXA;
    }

    /**
     * @return ClassXA
     */
    public function getClassXA()
    {
        return $this->classXA;
    }


}
