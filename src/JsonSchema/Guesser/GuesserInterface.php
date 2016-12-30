<?php

namespace Joli\Jane\JsonSchema\Guesser;

interface GuesserInterface
{
    /**
     * Is this object supported for the guesser
     *
     * @param $object
     *
     * @return bool
     */
    public function supportObject($object);
}
