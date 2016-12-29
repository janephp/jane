<?php

declare(strict_types=1);

namespace Joli\Jane\AstGenerator\Model;

use Joli\Jane\AstGenerator\AstGeneratorInterface;
use Joli\Jane\AstGenerator\Exception\NotSupportedGeneratorException;
use Joli\Jane\AstGenerator\Naming;
use PhpParser\Comment\Doc;
use PhpParser\Node\Name;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

abstract class Model implements AstGeneratorInterface
{
    /**
     * @var PropertyInfoExtractorInterface
     */
    protected $propertyInfoExtractor;

    public function __construct(PropertyInfoExtractorInterface $propertyInfoExtractor, PropertyTypeExtractorInterface $propertyTypeExtractor)
    {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, array $context = [])
    {
        if (!is_string($object) || count($this->propertyInfoExtractor->getProperties($object)) === 0) {
            throw new NotSupportedGeneratorException();
        }

        $properties = [];
        $methods = [];

        foreach ($this->propertyInfoExtractor->getProperties($object) as $property) {
            $properties[] = $this->createProperty($object, $property);
            $methods[] = $this->createGetter($object, $property);
            $methods[] = $this->createSetter($object, $property);
        }

        return new Stmt\Class_(
            new Name(Naming::getClassName($object)),
            [
                'stmts' => array_merge($properties, $methods),
            ]
        );
    }

    /**
     * Create setter method.
     *
     * @param string $class
     * @param string $property
     *
     * @return Stmt\ClassMethod
     */
    abstract public function createSetter($class, $property);

    /**
     * Create getter method.
     *
     * @param string $class
     * @param string $property
     *
     * @return Stmt\ClassMethod
     */
    private function createGetter($class, $property)
    {
        $methodName = Naming::getPrefixedMethodName('get', $property);
        $propertyName = Naming::getPropertyName($property);

        return new Stmt\ClassMethod(
            $methodName,
            [
                // public function
                'type' => Stmt\Class_::MODIFIER_PUBLIC,
                'stmts' => [
                    // return $this->property;
                    new Stmt\Return_(
                        new Expr\PropertyFetch(new Expr\Variable('this'), $propertyName)
                    ),
                ],
            ],
            [
                'comments' => [new Doc(sprintf(<<<'EOD'
/**
 * @return %s
 */
EOD
                    , $this->getDocType($class, $property)))],
            ]
        );
    }

    /**
     * Return a property stmt.
     *
     * @param string $class
     * @param string $property
     *
     * @return Stmt\Property
     */
    protected function createProperty($class, $property)
    {
        $propertyName = Naming::getPropertyName($property);
        $property = new Stmt\PropertyProperty($propertyName);

        return new Stmt\Property(Stmt\Class_::MODIFIER_PROTECTED, [
            $property,
        ], [
            'comments' => [new Doc(sprintf(<<<'EOD'
/**
 * @var %s
 */
EOD
                , $this->getDocType($class, $property)))],
        ]);
    }

    /**
     * Get type to show on a documentation.
     *
     * @param string $class
     * @param string $property
     *
     * @return string
     */
    final protected function getDocType($class, $property)
    {
        $types = $this->propertyInfoExtractor->getTypes($class, $property);

        if ($types === null || count($types) === 0) {
            return 'mixed';
        }

        $typesString = [];

        foreach ($types as $type) {
            $typeString = $type->getClassName() === null ? $type->getBuiltinType() : $type->getClassName();

            if ($type->isCollection()) {
                $typeString .= '[]';
            }

            $typesString[] = $typeString;
        }

        return implode('|', $typesString);
    }
}
