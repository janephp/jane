<?php

namespace Joli\Jane\AstGenerator\Generator;

use Joli\Jane\AstGenerator\Extractor\ClassInfoExtractorInterface;
use Joli\Jane\AstGenerator\Extractor\ClassNamespaceExtractorInterface;
use Joli\Jane\AstGenerator\Generator\Exception\NotSupportedGeneratorException;
use Joli\Jane\AstGenerator\Naming;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

/**
 * Schema Generator is a generator to create a set of classes around a schema.
 *
 * It can generate :
 *   - POPO Classes (simple class with getter and setter)
 *   - Normalizers of the POPO classes
 *   - ....
 */
class SchemaGenerator implements AstGeneratorInterface
{
    /** @var AstGeneratorInterface */
    private $popoGenerator;

    /** @var AstGeneratorInterface */
    private $normalizerGenerator;

    /** @var ClassInfoExtractorInterface */
    private $classExtractor;

    public function __construct(ClassInfoExtractorInterface $classExtractor)
    {
        $this->classExtractor = $classExtractor;
    }

    /**
     * Allow this generator to generator POPO Classes
     *
     * @param AstGeneratorInterface $popoGenerator
     */
    public function setPopoGenerator(AstGeneratorInterface $popoGenerator)
    {
        $this->popoGenerator = $popoGenerator;
    }

    /**
     * Allow this generator to generator normalizers
     *
     * @param AstGeneratorInterface $normalizerGenerator
     */
    public function setNormalizerGenerator(AstGeneratorInterface $normalizerGenerator)
    {
        $this->normalizerGenerator = $normalizerGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, array $context = array())
    {
        if (!is_string($object) || count($this->classExtractor->getClasses($object)) === 0) {
            throw new NotSupportedGeneratorException();
        }

        /** @var ClassNamespaceExtractorInterface $normalizerNamespaceExtractor */
        $normalizerNamespaceExtractor = $this->classExtractor;

        if (array_key_exists('normalizer-namespace-class-extractor', $context) && $context['normalizer-namespace-class-extractor'] instanceof ClassNamespaceExtractorInterface) {
            $normalizerNamespaceExtractor = $context['normalizer-namespace-class-extractor'];
        }

        $namespaces = [];

        $getOrCreateNamespace = function ($namespace) use (&$namespaces) {
            if (!array_key_exists($namespace, $namespaces)) {
                $namespaces[$namespace] = new Stmt\Namespace_(new Name($namespace));
            }

            return $namespaces[$namespace];
        };


        foreach ($this->classExtractor->getClasses($object) as $class) {
            $classNamespace = $this->classExtractor->getNamespace($object, $class);
            $classFqdn = $classNamespace . '\\' . Naming::getClassName($class);

            if ($this->popoGenerator !== null) {
                $namespace = $getOrCreateNamespace($classNamespace);
                $namespace->stmts = array_merge($namespace->stmts, $this->popoGenerator->generate($classFqdn, [
                    'short_class_name' => Naming::getClassName($class)
                ]));
            }

            if ($this->normalizerGenerator !== null) {
                $namespace = $getOrCreateNamespace($normalizerNamespaceExtractor->getNamespace($object, $class));
                $namespace->stmts = array_merge($namespace->stmts, $this->normalizerGenerator->generate($classFqdn, [
                    'short_class_name' => Naming::getClassName($class . 'Normalize')
                ]));
            }
        }

        return array_values($namespaces);
    }
}
