<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Registry;

interface ClassGuesserInterface
{
    /**
     * Guess model
     *
     * This guesser should create a Model and the associated File
     * The file must be inject into the context
     *
     * @param mixed    $object
     * @param string   $name
     * @param string   $reference Json ref to the class
     * @param Registry $registry  Registry
     */
    public function guessClass($object, $name, $reference, Registry $registry);
}
