<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\File;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

use Joli\Jane\Reference\Reference;
use Joli\Jane\Reference\Resolver;
use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

class ObjectType extends AbstractType
{
    /**
     * @var \Joli\Jane\Generator\TypeDecisionManager
     */
    private $typeDecisionManager;

    /**
     * @var \Joli\Jane\Reference\Resolver
     */
    private $resolver;

    public function __construct(TypeDecisionManager $typeDecisionManager, Resolver $resolver)
    {
        $this->typeDecisionManager = $typeDecisionManager;
        $this->resolver            = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function generateObject($schema, $name, Context $context)
    {
        if ($schema->getDefinitions() !== null) {
            foreach ($schema->getDefinitions() as $key => $definition) {
                $this->typeDecisionManager->resolveType($definition)->generateObject($definition, $key, $context);
            }
        }

        $factory  = new BuilderFactory();
        $ast      = $factory->namespace($context->getNamespace() . '\Model');
        $astClass = $factory->class(ucfirst($name));

        $context->getSchemaObjectMap()->addSchemaObject($schema, ucfirst($name));

        if ($schema->getAdditionalProperties() !== false || is_array($schema->getPatternProperties())) {
            $astClass->extend('\ArrayObject');
        }

        if ($schema->getProperties() !== null) {
            foreach ($schema->getProperties() as $key => $property) {
                $subType       = $this->typeDecisionManager->resolveType($property);
                $propertyExpr  = $subType->getProperty($property, $key, $context);

                if ($propertyExpr !== null) {
                    $astClass->addStmt($propertyExpr);
                }

                foreach ($subType->getMethods($property, $key, $context) as $method) {
                    $astClass->addStmt($method);
                }
            }
        }

        $ast->addStmt($astClass);
        $context->addFile(new File($context->getDirectory() . '/Model/'.ucfirst($name).'.php', $ast->getNode()));
    }

    /**
     * {@inheritDoc}
     */
    public function generateNormalizer($schema, $name, Context $context)
    {
        $factory  = new BuilderFactory();

        $context->getSchemaObjectNormalizerMap()->addSchemaObject($schema, ucfirst($name).'Normalizer');
        $astClass = $factory->class(ucfirst($name).'Normalizer')
            ->implement('DenormalizerInterface')
            ->addStmt($factory->property('normalizerChain'))
            ->addStmt(
                $factory->method('setNormalizerChain')
                    ->makePublic()
                    ->addParam($factory->param('normalizerChain')->setTypeHint('NormalizerChain'))
                    ->addStmt(
                        new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), 'normalizerChain'), new Expr\Variable('normalizerChain'))
                    )
            )
            ->addStmt(
                $factory->method('supportsDenormalization')
                    ->makePublic()
                    ->addParam($factory->param('data'))
                    ->addParam($factory->param('type'))
                    ->addParam($factory->param('format')->setDefault(null))
                    ->addStmts([
                            new Stmt\If_(
                                new Expr\BinaryOp\NotIdentical(new Expr\Variable('type'), new Scalar\String_(sprintf('%s\\Model\\%s', $context->getNamespace(), ucfirst($name)))),
                                [
                                    'stmts' => [
                                        new Stmt\Return_(new Expr\ConstFetch(new Name("false")))
                                    ]
                                ]
                            ),
                            new Stmt\If_(
                                new Expr\BinaryOp\NotIdentical(new Expr\Variable('format'), new Scalar\String_('json')),
                                [
                                    'stmts' => [
                                        new Stmt\Return_(new Expr\ConstFetch(new Name("false")))
                                    ]
                                ]
                            ),
                            new Stmt\Return_(new Expr\ConstFetch(new Name("true")))
                        ]
                    )
            )
        ;

        $denormalizeMethod = $factory->method('denormalize')
            ->makePublic()
            ->addParam($factory->param('data'))
            ->addParam($factory->param('class'))
            ->addParam($factory->param('format')->setDefault(null))
            ->addParam($factory->param('context')->setDefault(array())->setTypeHint('array'))
        ;

        $objectVariable = new Expr\Variable('object');
        $statements     = [
            new Stmt\If_(
                new Expr\Empty_(new Expr\Variable('data')),
                [
                    'stmts' => [
                        new Stmt\Return_(new Expr\ConstFetch(new Name("null")))
                    ]
                ]
            ),
            new Stmt\If_(
                new Expr\Isset_([new Expr\PropertyFetch(new Expr\Variable('data'), "{'\$ref'}")]),
                [
                    'stmts' => [
                        new Stmt\Return_(new Expr\New_(new Name('Reference'), [
                            new Expr\PropertyFetch(new Expr\Variable('data'), "{'\$ref'}"),
                            new Expr\Ternary(new Expr\ArrayDimFetch(new Expr\Variable('context'), new Scalar\String_('rootSchema')), null, new Expr\ConstFetch(new Name("null")))
                        ]))
                    ]
                ]
            ),
            new Expr\Assign($objectVariable, new Expr\New_(new Name(sprintf('\\%s\\Model\\%s', $context->getNamespace(), ucfirst($name))))),
            new Stmt\If_(
                new Expr\BooleanNot(new Expr\Isset_([new Expr\ArrayDimFetch(new Expr\Variable('context'), new Scalar\String_('rootSchema'))])),
                [
                    'stmts' => [
                        new Expr\Assign(new Expr\ArrayDimFetch(new Expr\Variable('context'), new Scalar\String_('rootSchema')), $objectVariable)
                    ]
                ]
            ),
        ];

        foreach ($schema->getProperties() as $key => $property) {
            $propertyVar               = new Expr\PropertyFetch(new Expr\Variable('data'), sprintf("{'%s'}", $key));
            list($ifStmts, $outputVar) = $this->typeDecisionManager->resolveType($property)->getDenormalizationStmt($property, $key, $context, $propertyVar);
            $statements[]              = new Stmt\If_(
                new Expr\Isset_([$propertyVar]),
                [
                    'stmts' => array_merge($ifStmts, [
                        new Expr\MethodCall($objectVariable, 'set'.ucfirst($this->encodePropertyName($key)), [
                            $outputVar
                        ])
                    ])
                ]
            );
        }

        $statements[] = new Stmt\Return_($objectVariable);

        $denormalizeMethod->addStmts($statements);
        $astClass->addStmt($denormalizeMethod);

        $node = $factory->namespace($context->getNamespace() . '\\Normalizer')
            ->addStmt($factory->use('Joli\Jane\Reference\Reference'))
            ->addStmt($factory->use('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'))
            ->addStmt($astClass)
            ->getNode()
        ;

        $context->addFile(new File($context->getDirectory() . DIRECTORY_SEPARATOR . 'Normalizer' . DIRECTORY_SEPARATOR . ucfirst($name) . 'Normalizer.php', $node));
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationStmt($schema, $name, Context $context, Expr $input)
    {
        if (!$context->getSchemaObjectNormalizerMap()->hasSchema($schema)) {
            $this->typeDecisionManager->resolveType($schema)->generateNormalizer($schema, $name, $context);
        }

        return parent::getDenormalizationStmt($schema, $name, $context, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationIfStmt($schema, $name, Context $context, Expr $input)
    {
        $determinant = [];
        $required    = $schema->getRequired() ?: [];

        foreach ($schema->getProperties() as $key => $property) {
            if (!in_array($key, $required)) {
                continue;
            }

            if ($property instanceof Reference) {
                $property = $this->resolver->resolve($property);
            }

            if ($property->getEnum() !== null) {
                $isSimple = true;

                foreach ($property->getEnum() as $value) {
                    if (is_array($value) || is_object($value)) {
                        $isSimple = false;
                    }
                }

                if ($isSimple) {
                    $determinant[$key] = $property->getEnum();
                }
            }
        }

        $ifStmt     = new Expr\FuncCall(new Name('is_object'), [new Arg($input)]);
        $logicalAnd = null;

        foreach ($determinant as $key => $values) {
            $logicalOr = null;

            foreach ($values as $value) {
                if ($logicalOr === null) {
                    $logicalOr = new Expr\BinaryOp\Equal(
                        new Expr\PropertyFetch($input, sprintf("{'%s'}", $key)),
                        new Scalar\String_($value)
                    );
                } else {
                    $logicalOr = new Expr\BinaryOp\LogicalOr(
                        $logicalOr,
                        new Expr\BinaryOp\Equal(
                            new Expr\PropertyFetch($input, sprintf("{'%s'}", $key)),
                            new Scalar\String_($value)
                        )
                    );
                }
            }

            if ($logicalOr !== null) {
                $ifStmt = new Expr\BinaryOp\LogicalAnd($ifStmt, $logicalOr);
            }
        }

        return $ifStmt;
    }

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && $schema->getType() === 'object' && $schema->getProperties() !== null);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        return ["\\". $context->getNamespace()."\\".ucfirst($name)];
    }
}
