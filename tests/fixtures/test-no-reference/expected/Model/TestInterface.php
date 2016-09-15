<?php

namespace Joli\Jane\Tests\Expected\Model;

interface TestInterface
{
    /**
     * @return string
     */
    public function getString();

    /**
     * @param string $string
     *
     * @return self
     */
    public function setString($string = null);
}
