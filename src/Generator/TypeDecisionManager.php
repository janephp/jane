<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Type\ArrayObjectType;
use Joli\Jane\Generator\Type\ArrayType;
use Joli\Jane\Generator\Type\BooleanType;
use Joli\Jane\Generator\Type\IntegerType;
use Joli\Jane\Generator\Type\MultipleType;
use Joli\Jane\Generator\Type\NullType;
use Joli\Jane\Generator\Type\NumberType;
use Joli\Jane\Generator\Type\ObjectType;
use Joli\Jane\Generator\Type\ReferenceType;
use Joli\Jane\Generator\Type\StringType;
use Joli\Jane\Generator\Type\TypeInterface;
use Joli\Jane\Generator\Type\UndefinedType;
use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;
use Joli\Jane\Model\JsonSchema;
use Symfony\Component\Serializer\Serializer;

class TypeDecisionManager
{
    /**
     * @var TypeInterface[]
     */
    private $types;

    /**
     * Add a type interface
     *
     * @param TypeInterface $type
     */
    public function addType(TypeInterface $type)
    {
        $this->types[] = $type;
    }

    /**
     * Resolve a type interface
     *
     * @param JsonSchema|Reference $schema
     *
     * @return TypeInterface|null
     */
    public function resolveType($schema)
    {
        foreach ($this->types as $type) {
            if ($type->supportSchema($schema)) {
                return $type;
            }
        }

        return null;
    }

    public static function build(Serializer $serializer)
    {
        $typeDecision = new self();

        $typeDecision->addType(new MultipleType($typeDecision));
        $typeDecision->addType(new BooleanType());
        $typeDecision->addType(new IntegerType());
        $typeDecision->addType(new NullType());
        $typeDecision->addType(new NumberType());
        $typeDecision->addType(new StringType());
        $typeDecision->addType(new ArrayType($typeDecision));
        $typeDecision->addType(new ArrayObjectType($typeDecision));
        $typeDecision->addType(new ObjectType($typeDecision));
        $typeDecision->addType(new ReferenceType(new Resolver($serializer), $typeDecision));
        $typeDecision->addType(new UndefinedType($typeDecision));

        return $typeDecision;
    }
}
