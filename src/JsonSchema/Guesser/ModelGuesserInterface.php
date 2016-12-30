<?php

namespace Joli\Jane\JsonSchema\Guesser;

use Joli\Jane\JsonSchema\Registry\Registry;

interface ModelGuesserInterface
{
    /**
     * Guess model
     *
     * This guesser should create a JsonSchema.Model and the associated File
     * The file must be inject into the context
     *
     * @param mixed    $object
     * @param string   $name
     * @param string   $reference Json reference to the class (unique identifier)
     * @param Registry $registry
     */
    public function registerModel($object, $name, $reference, Registry $registry);
}
