<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Guesser\Guess\Type;
use Joli\Jane\Registry;
use Joli\Jane\Schema;

interface TypeGuesserInterface
{
    /**
     * Return all types guessed
     *
     * @param mixed    $object
     * @param string   $name
     * @param Registry $registry
     * @param Schema   $schema
     *
     * @internal
     *
     * @return Type
     */
    public function guessType($object, $name, $reference, Registry $registry);
}
