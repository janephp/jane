<?php

namespace Joli\Jane\Tests\Expected\Model;

class Test
{
    /**
     * @var string
     */
    protected $string;
    /**
     * @var TestSubObject
     */
    protected $subObject;

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * @param string $string
     *
     * @return self
     */
    public function setString($string = null)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * @return TestSubObject
     */
    public function getSubObject()
    {
        return $this->subObject;
    }

    /**
     * @param TestSubObject $subObject
     *
     * @return self
     */
    public function setSubObject(TestSubObject $subObject = null)
    {
        $this->subObject = $subObject;

        return $this;
    }
}
