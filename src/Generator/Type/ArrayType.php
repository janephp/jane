<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class ArrayType extends AbstractType
{
    /**
     * @var \Joli\Jane\Generator\TypeDecisionManager
     */
    private $typeDecisionManager;

    public function __construct(TypeDecisionManager $typeDecisionManager)
    {
        $this->typeDecisionManager = $typeDecisionManager;
    }

    /**
     * {@inheritDoc}
     */
    public function generateObject($schema, $name, Context $context)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function generateNormalizer($schema, $name, Context $context)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationStmt($schema, $name, Context $context, Expr $input)
    {
        $items = $schema->getItems();

        if ($items === null) {
            return parent::getDenormalizationStmt($schema, $name, $context, $input);
        }

        $valuesVar     = new Expr\Variable($context->getUniqueVariableName('values'));
        $statements    = [
            // $values = [];
            new Expr\Assign($valuesVar, new Expr\Array_()),
        ];

        $loopStatements = [];
        $loopValueVar   = new Expr\Variable($context->getUniqueVariableName('value'));

        if (is_array($items)) {
            foreach ($items as $item) {
                list($subStatements, $outputExpr) = $this->typeDecisionManager->resolveType($item)->getDenormalizationStmt($item, $name, $context, $loopValueVar);

                $loopStatements[] = new Stmt\If_(
                    $this->typeDecisionManager->resolveType($item)->getDenormalizationIfStmt($item, $name, $context, $loopValueVar),
                    [
                        'stmts' => array_merge($subStatements, [
                            new Expr\Assign(new Expr\ArrayDimFetch($valuesVar), $outputExpr),
                            new Stmt\Continue_()
                        ])
                    ]
                );
            }
        } else {
            list($subStatements, $outputExpr) = $this->typeDecisionManager->resolveType($items)->getDenormalizationStmt($items, $name, $context, $loopValueVar);

            $loopStatements = array_merge($loopStatements, $subStatements);
            $loopStatements[] = new Expr\Assign(new Expr\ArrayDimFetch($valuesVar), $outputExpr);
        }

        $statements[] = new Stmt\Foreach_($input, $loopValueVar, [
            'stmts'  => $loopStatements
        ]);

        return [$statements, $valuesVar];
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        return new Expr\FuncCall(new Name('is_array'), [new Arg($input)]);
    }

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && $schema->getType() === 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $items = $schema->getItems();

        if ($items === null) {
            return ['array'];
        }

        if (is_array($items)) {
            $types = [];

            foreach ($items as $item) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($item)->getPhpTypes($item, $name, $context));
            }
        } else {
            $types = $this->typeDecisionManager->resolveType($items)->getPhpTypes($items, $name, $context);
        }

        $types = array_map(function ($type) {
            return $type.'[]';
        }, $types);

        return $types;
    }
}
