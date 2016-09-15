<?php

namespace Joli\Jane\Tests\Expected\Model;

interface TestInterface
{
    public function getOnlyNull();

    /**
     * @param null $onlyNull
     *
     * @return self
     */
    public function setOnlyNull($onlyNull = null);

    /**
     * @return string|null
     */
    public function getNullOrString();

    /**
     * @param string|null $nullOrString
     *
     * @return self
     */
    public function setNullOrString($nullOrString = null);
}
