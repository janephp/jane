<?php

namespace Joli\Jane\AstGenerator\Extractor;

/**
 * Extract a list of classes
 */
interface ClassListExtractorInterface
{
    /**
     * Get class for a specific domain
     *
     * @param string $domain
     *
     * @return string[]
     */
    public function getClasses($domain);
}
