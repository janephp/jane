<?php

namespace Joli\Jane\JsonSchema\Guesser;

use Joli\Jane\JsonSchema\Registry\Property;
use Joli\Jane\JsonSchema\Registry\Registry;

interface PropertiesGuesserInterface
{
    /**
     * Return all properties guessed
     *
     * @param mixed    $object
     * @param string   $name
     * @param Registry $registry
     *
     * @return Property[]
     */
    public function guessProperties($object, $name, Registry $registry);
}
