<?php

namespace Joli\Jane\JsonSchema\Registry;

trait OriginAwareTrait
{
    /** @var mixed */
    protected $origin;

    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }
}
