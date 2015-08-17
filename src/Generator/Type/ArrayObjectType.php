<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Reference\Reference;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

class ArrayObjectType extends AbstractType
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
        $additionalProperties = $schema->getAdditionalProperties();
        $patternProperties = $schema->getPatternProperties();

        if (($additionalProperties === null || $additionalProperties === false) && ($patternProperties === null || $patternProperties === false)) {
            return parent::getDenormalizationStmt($schema, $name, $context, $input);
        }

        $valuesVar     = new Expr\Variable($context->getUniqueVariableName('values'));
        $statements    = [
            // $values = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);
            new Expr\Assign($valuesVar, new Expr\New_(new Name('\ArrayObject'), [
                new Expr\Array_(),
                new Expr\ClassConstFetch(new Name('\ArrayObject'), 'ARRAY_AS_PROPS')
            ])),
        ];

        $loopStatements = [];
        $loopKeyVar     = new Expr\Variable($context->getUniqueVariableName('key'));
        $loopValueVar   = new Expr\Variable($context->getUniqueVariableName('value'));

        if (!empty($patternProperties)) {
            foreach ($patternProperties as $pattern => $patternSchema) {
                list($subStatements, $outputExpr) = $this->typeDecisionManager->resolveType($patternSchema)->getDenormalizationStmt($patternSchema, $name, $context, $loopValueVar);

                $loopStatements[] = new Stmt\If_(
                    new Expr\FuncCall(new Name('preg_match'), [
                        new Arg(new Expr\ConstFetch(new Name("'/".str_replace('/', '\/', $pattern)."/'"))),
                        new Arg($loopKeyVar)
                    ]),
                    [
                        'stmts' => array_merge($subStatements, [
                            new Expr\Assign(new Expr\ArrayDimFetch($valuesVar, $loopKeyVar), $outputExpr),
                            new Stmt\Continue_()
                        ])
                    ]
                );
            }
        }

        if ($additionalProperties !== null && $additionalProperties !== false) {
            list($subStatements, $outputExpr) = $this->typeDecisionManager->resolveType($additionalProperties)->getDenormalizationStmt($additionalProperties, $name, $context, $loopValueVar);

            $loopStatements = array_merge($loopStatements, $subStatements);
            $loopStatements[] = new Expr\Assign(new Expr\ArrayDimFetch($valuesVar, $loopKeyVar), $outputExpr);
        }

        $statements[] = new Stmt\Foreach_($input, $loopValueVar, [
            'keyVar' => $loopKeyVar,
            'stmts'  => $loopStatements
        ]);

        return [$statements, $valuesVar];
    }

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        if ($schema instanceof Reference) {
            return false;
        }

        if ($schema->getType() !== 'object') {
            return false;
        }

        if (!empty($schema->getProperties())) {
            return false;
        }

        if (($schema->getAdditionalProperties() === false || $schema->getAdditionalProperties() === null) && ($schema->getPatternProperties() === false || $schema->getPatternProperties() === null)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        return new Expr\FuncCall(new Name('is_object'), [new Arg($input)]);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $additionalProperties = $schema->getAdditionalProperties();
        $patternProperties    = $schema->getPatternProperties();

        if (($additionalProperties === null || $additionalProperties === true) && ($patternProperties === null || $patternProperties === false)) {
            return ['array'];
        }

        $types = [];

        if (is_object($additionalProperties)) {
            $types = array_merge($types, $this->typeDecisionManager->resolveType($additionalProperties)->getPhpTypes($additionalProperties, $name, $context));
        }

        if (is_object($patternProperties)) {
            foreach ($patternProperties as $patternSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($patternSchema)->getPhpTypes($patternSchema, $name, $context));
            }
        }

        $types = array_map(function ($type) {
            return $type.'[]';
        }, $types);

        return $types;
    }
}
