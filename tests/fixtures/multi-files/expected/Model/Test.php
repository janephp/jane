<?php

namespace Joli\Jane\Tests\Expected\Model;

class Test
{
    /**
     * @var Foo
     */
    protected $foo;

    /**
     * @return Foo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param Foo $foo
     *
     * @return self
     */
    public function setFoo(Foo $foo = null)
    {
        $this->foo = $foo;

        return $this;
    }
}
