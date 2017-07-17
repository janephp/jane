<?php

namespace Joli\Jane\Tests\Expected\Model;

class Test
{
    /**
     * @var string
     */
    protected $string;
    /**
     * @var TestsubObject
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
     * @return TestsubObject
     */
    public function getSubObject()
    {
        return $this->subObject;
    }

    /**
     * @param TestsubObject $subObject
     *
     * @return self
     */
    public function setSubObject(TestsubObject $subObject = null)
    {
        $this->subObject = $subObject;

        return $this;
    }
}
