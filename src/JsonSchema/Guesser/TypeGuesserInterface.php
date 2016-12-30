<?php

namespace Joli\Jane\JsonSchema\Guesser;

use Joli\Jane\JsonSchema\Registry\Registry;
use Symfony\Component\PropertyInfo\Type;

interface TypeGuesserInterface
{
    /**
     * Return all types guessed
     *
     * @param mixed    $object
     * @param string   $name
     * @param Registry $registry
     *
     * @return Type[]
     */
    public function guessTypes($object, $name, Registry $registry);
}
