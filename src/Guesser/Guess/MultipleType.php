<?php

namespace Joli\Jane\Guesser\Guess;

use Joli\Jane\Generator\Context\Context;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

class MultipleType extends Type
{
    protected $types;

    public function __construct($object, array $types = array())
    {
        parent::__construct($object, 'mixed');

        $this->types = $types;
    }

    /**
     * Add a type
     *
     * @param Type $type
     *
     * @return $this
     */
    public function addType(Type $type)
    {
        if ($type instanceof MultipleType) {
            foreach ($type->getTypes() as $subType) {
                $this->types[] = $subType;
            }

            return $this;
        }

        $this->types[] = $type;

        return $this;
    }

    /**
     * Return a list of types
     *
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $stringTypes = array_map(function ($type) {
            return $type->__toString();
        }, $this->types);

        return implode('|', $stringTypes);
    }


    /**
     * (@inheritDoc}
     */
    public function createDenormalizationStatement(Context $context, Expr $input)
    {
        $output     = new Expr\Variable($context->getUniqueVariableName('value'));
        $statements = [
            new Expr\Assign($output, $input)
        ];

        foreach ($this->getTypes() as $type) {
            list ($typeStatements, $typeOutput) = $type->createDenormalizationStatement($context, $input);

            $statements[] = new Stmt\If_(
                $this->createItemConditionStatement($type, $input),
                [
                    'stmts' => array_merge(
                        $typeStatements, [
                            new Expr\Assign($output, $typeOutput)
                        ]
                    )
                ]
            );
        }

        return [$statements, $output];
    }

    protected function createItemConditionStatement(Type $type, Expr $input)
    {
        return $type->createConditionStatement($input);
    }
}
 