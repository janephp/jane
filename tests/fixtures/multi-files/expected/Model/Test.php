<?php

namespace Joli\Jane\Tests\Expected\Model;

class Test
{
    /**
     * @var TestFoo
     */
    protected $foo;

    /**
     * @return TestFoo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param TestFoo $foo
     *
     * @return self
     */
    public function setFoo(TestFoo $foo = null)
    {
        $this->foo = $foo;

        return $this;
    }
}
