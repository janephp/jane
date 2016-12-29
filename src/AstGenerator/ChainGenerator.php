<?php

declare(strict_types=1);

namespace Joli\Jane\AstGenerator;

use Joli\Jane\AstGenerator\Exception\NotSupportedGeneratorException;

/**
 * AstGenerator delegating the generation to a chain of generators.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class ChainGenerator implements AstGeneratorInterface
{
    /** @var AstGeneratorInterface[] A list of generators */
    protected $generators;

    /** @var bool Whether the generation must return as soon as possible or use all generators, default to false */
    protected $returnOnFirst;

    public function __construct(array $generators = [], $returnOnFirst = false)
    {
        $this->generators = $generators;
        $this->returnOnFirst = $returnOnFirst;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, array $context = [])
    {
        $nodes = [];
        $lastException = new NotSupportedGeneratorException('No generators');

        foreach ($this->generators as $generator) {
            try {
                $nodes = array_merge($nodes, $generator->generate($object, $context));

                if ($this->returnOnFirst) {
                    return $nodes;
                }
            } catch (NotSupportedGeneratorException $exception) {
                $lastException = $exception;

                continue;
            }
        }

        if (empty($nodes)) {
            throw $lastException;
        }

        return $nodes;
    }
}
