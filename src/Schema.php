<?php

namespace Joli\Jane;

use Joli\Jane\Guesser\Guess\ClassGuess;

class Schema
{
    /** @var string Origin of the schema (file or url path) */
    private $origin;

    /** @var string Namespace wanted for this schema */
    private $namespace;

    /** @var string Directory where to put files */
    private $directory;

    /** @var string Name of the root object in the schema (if needed) */
    private $rootName;

    /** @var ClassGuess[] List of classes associated to this schema */
    private $classes = [];

    /** @var string[] A list of references this schema is registered to */
    private $references;

    /** @var mixed Parsed schema */
    private $parsed;

    /**
     * Schema constructor.
     * @param string $origin
     * @param string $namespace
     * @param string $directory
     * @param string $rootName
     */
    public function __construct($origin, $namespace, $directory, $rootName)
    {
        $this->origin = $origin;
        $this->namespace = $namespace;
        $this->directory = $directory;
        $this->rootName = $rootName;
        $this->references = [$origin];
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function getRootName()
    {
        return $this->rootName;
    }

    public function addClass($reference, $class)
    {
        $this->classes[$reference] = $class;
    }

    public function getClass($reference)
    {
        if (!array_key_exists($reference, $this->classes)) {
            return null;
        }

        return $this->classes[$reference];
    }

    /**
     * @return ClassGuess[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    public function addReference($reference)
    {
        $this->references[] = $reference;
    }

    public function hasReference($reference)
    {
        return in_array($reference, $this->references, true);
    }

    /**
     * @return mixed
     */
    public function getParsed()
    {
        return $this->parsed;
    }

    /**
     * @param mixed $parsed
     */
    public function setParsed($parsed)
    {
        $this->parsed = $parsed;
    }
}
