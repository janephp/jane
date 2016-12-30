<?php

namespace Joli\Jane\AstGenerator\Extractor;

interface ClassNamespaceExtractorInterface
{
    /**
     * Return the namespace for a class
     *
     * @param string $domain
     * @param string $class
     *
     * @return string
     */
    public function getNamespace($domain, $class);
}
