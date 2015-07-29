<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class UndefinedType extends AbstractType
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
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && $schema->getType() === null);
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationStmt($schema, $name, Context $context, Expr $input)
    {
        if ($schema->getAnyOf() === null && $schema->getAllOf() === null && $schema->getOneOf() === null) {
            return parent::getDenormalizationStmt($schema, $name, $context, $input);
        }

        $valueVar   = new Expr\Variable($context->getUniqueVariableName('value'));
        $statements = [
            new Expr\Assign($valueVar, new Expr\ConstFetch(new Name("null")))
        ];

        if ($schema->getAnyOf() !== null) {
            foreach ($schema->getAnyOf() as $subSchema) {
                list($ifStmts, $outputExpr) = $this->typeDecisionManager->resolveType($subSchema)->getDenormalizationStmt($subSchema, $name, $context, $input);

                $statements[] = new Stmt\If_(
                    $this->typeDecisionManager->resolveType($subSchema)->getDenormalizationIfStmt($subSchema, $name, $context, $input),
                    [
                        'stmts' => array_merge(
                            $ifStmts,
                            [new Expr\Assign($valueVar, $outputExpr)]
                        )
                    ]
                );
            }
        }

        if ($schema->getAllOf() !== null) {
            foreach ($schema->getAllOf() as $subSchema) {
                list($ifStmts, $outputExpr) = $this->typeDecisionManager->resolveType($subSchema)->getDenormalizationStmt($subSchema, $name, $context, $input);

                $statements[] = new Stmt\If_(
                    $this->typeDecisionManager->resolveType($subSchema)->getDenormalizationIfStmt($subSchema, $name, $context, $input),
                    [
                        'stmts' => array_merge(
                            $ifStmts,
                            [new Expr\Assign($valueVar, $outputExpr)]
                        )
                    ]
                );
            }
        }

        if ($schema->getOneOf() !== null) {
            foreach ($schema->getOneOf() as $subSchema) {
                list($ifStmts, $outputExpr) = $this->typeDecisionManager->resolveType($subSchema)->getDenormalizationStmt($subSchema, $name, $context, $input);

                $statements[] = new Stmt\If_(
                    $this->typeDecisionManager->resolveType($subSchema)->getDenormalizationIfStmt($subSchema, $name, $context, $input),
                    [
                        'stmts' => array_merge(
                            $ifStmts,
                            [new Expr\Assign($valueVar, $outputExpr)]
                        )
                    ]
                );
            }
        }

        return [$statements, $valueVar];
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        return new Expr\FuncCall(new Name('isset'), [new Arg($input)]);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $types = [];

        if ($schema->getAnyOf() !== null) {
            foreach ($schema->getAnyOf() as $subSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($subSchema)->getPhpTypes($subSchema, $name, $context));
            }
        }

        if ($schema->getAllOf() !== null) {
            foreach ($schema->getAllOf() as $subSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($subSchema)->getPhpTypes($subSchema, $name, $context));
            }
        }

        if ($schema->getOneOf() !== null) {
            foreach ($schema->getOneOf() as $subSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($subSchema)->getPhpTypes($subSchema, $name, $context));
            }
        }

        if (empty($types)) {
            return ['mixed'];
        }

        return $types;
    }
}
