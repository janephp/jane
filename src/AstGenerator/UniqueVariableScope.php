<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator;

/**
 * Allow to get a unique variable name for a scope (like a method).
 */
class UniqueVariableScope
{
    private $registry = array();

    /**
     * Return an unique name for a variable.
     *
     * @param string $name Name of the variable
     *
     * @return string if not found return the $name given, if not return the name suffixed with a number
     */
    public function getUniqueName($name)
    {
        if (!isset($this->registry[$name])) {
            $this->registry[$name] = 0;

            return $name;
        }

        ++$this->registry[$name];

        return sprintf('%s_%s', $name, $this->registry[$name]);
    }
}
