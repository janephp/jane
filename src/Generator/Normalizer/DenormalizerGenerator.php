<?php

namespace Joli\Jane\Generator\Normalizer;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\Naming;
use Joli\Jane\Guesser\Guess\MultipleType;
use Joli\Jane\Guesser\Guess\Type;
use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

trait DenormalizerGenerator
{
    /**
     * The naming service
     *
     * @return Naming
     */
    abstract protected function getNaming();

    /**
     * Create method to check if denormalization is supported
     *
     * @param string $modelFqdn Fully Qualified name of the model class denormalized
     *
     * @return Stmt\ClassMethod
     */
    protected function createSupportsDenormalizationMethod($modelFqdn)
    {
        return new Stmt\ClassMethod('supportsDenormalization', [
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => [
                new Param('data'),
                new Param('type'),
                new Param('format', new Expr\ConstFetch(new Name("null"))),
            ],
            'stmts' => [
                new Stmt\If_(
                    new Expr\BinaryOp\NotIdentical(new Expr\Variable('type'), new Scalar\String_($modelFqdn)),
                    [
                        'stmts' => [
                            new Stmt\Return_(new Expr\ConstFetch(new Name("false")))
                        ]
                    ]
                ),
                new Stmt\Return_(new Expr\ConstFetch(new Name("true")))
            ]
        ]);
    }

    /**
     * Create the denormalization method
     *
     * @param $modelFqdn
     * @param Context  $context
     * @param $properties
     *
     * @return Stmt\ClassMethod
     */
    protected function createDenormalizeMethod($modelFqdn, Context $context, $properties)
    {
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
            new Expr\Assign($objectVariable, new Expr\New_(new Name("\\".$modelFqdn))),
            new Stmt\If_(
                new Expr\BooleanNot(new Expr\Isset_([new Expr\ArrayDimFetch(new Expr\Variable('context'), new Scalar\String_('rootSchema'))])),
                [
                    'stmts' => [
                        new Expr\Assign(new Expr\ArrayDimFetch(new Expr\Variable('context'), new Scalar\String_('rootSchema')), $objectVariable)
                    ]
                ]
            ),
        ];

        foreach ($properties as $property) {
            $propertyVar = new Expr\PropertyFetch(new Expr\Variable('data'), sprintf("{'%s'}", $property->getName()));

            /** @var Type $type */
            $type = $property->getType();
            $else = null;

            if ('null' === $type->getName()) {
                $ifCondition = new Expr\FuncCall(
                    new Name('property_exists'),
                    [
                        new Arg($objectVariable),
                        New Arg(new Scalar\String_($property->getName())),
                    ]
                );
            } else {
                $ifCondition = new Expr\Isset_([$propertyVar]);

                if ($type instanceof MultipleType) {
                    $types = $type->getTypes();

                    foreach ($types as $key => $childType) {
                        if ('null' === $childType->getName()) {
                            unset($types[$key]);
                            $type = new MultipleType($type->getObject(), $types);

                            $else = new Stmt\Else_([
                                new Expr\MethodCall(
                                    $objectVariable,
                                    $this->getNaming()->getPrefixedMethodName('set', $property->getName()),
                                    [
                                        new Expr\ConstFetch(new Name('null')),
                                    ]
                                )
                            ]);
                        }
                    }
                }
            }

            list($denormalizationStatements, $outputVar) = $type->createDenormalizationStatement($context, $propertyVar);

            $statements[] = new Stmt\If_(
                $ifCondition,
                [
                    'stmts' => array_merge($denormalizationStatements, [
                        new Expr\MethodCall($objectVariable, $this->getNaming()->getPrefixedMethodName('set', $property->getName()), [
                            $outputVar
                        ])
                    ]),
                    'else' => $else,
                ]
            );
        }

        $statements[] = new Stmt\Return_($objectVariable);

        return new Stmt\ClassMethod('denormalize', [
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => [
                new Param('data'),
                new Param('class'),
                new Param('format', new Expr\ConstFetch(new Name("null"))),
                new Param('context', new Expr\Array_(), 'array'),
            ],
            'stmts' => $statements
        ]);
    }
}
