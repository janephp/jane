<?php

namespace Joli\Jane\Reference;

use Joli\Jane\Schema\Schema;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Resolver
{
    /**
     * Resolve a JSON Reference for a Schema
     *
     * @param Reference $reference
     * @param Schema $root
     *
     * @throws UnsupportedException
     *
     * @return mixed Return the json value (deserialized) referenced
     */
    public function resolve(Reference $reference, Schema $root)
    {
        if (!$reference->isInCurrentDocument() || !$reference->hasFragment()) {
            throw new UnsupportedException("Only json pointer to the current document is supported at this time");
        }

        return $this->resolveJSONPointer($reference->getFragment(), $root);
    }
    /**
     * Resolve a JSON Pointer for a Schema
     *
     * @param string $pointer
     * @param Schema $root
     *
     * @return mixed Return the json value (deserialized) referenced
     */
    protected function resolveJSONPointer($pointer, Schema $root)
    {
        if (empty($pointer)) {
            return $root;
        }

        // Separate pointer into tokens
        $tokens = explode('/', $pointer);
        array_shift($tokens);
        // Unescape token
        $tokens = array_map(function ($token) {
            $token = str_replace('~0', '/', $token);
            $token = str_replace('~1', '~', $token);

            return $token;
        }, $tokens);

        $propertyPath     = implode(".", $tokens);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($root, $propertyPath);
    }
}
 