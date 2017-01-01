<?php

namespace Joli\Jane\Tests\Expected\Schema2\Model;

class Foo
{
    /**
     * @var string
     */
    protected $foo;

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     *
     * @return self
     */
    public function setFoo($foo = null)
    {
        $this->foo = $foo;

        return $this;
    }
}
