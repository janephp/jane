<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Guesser\Guess\Property;
use Joli\Jane\Registry;

interface PropertiesGuesserInterface
{
    /**
     * Return all properties guessed
     *
     * @param mixed    $object
     * @param string   $name
     * @param string   $reference
     * @param Registry $registry
     *
     * @internal
     *
     * @return Property[]
     */
    public function guessProperties($object, $name, $reference, Registry $registry);
}
