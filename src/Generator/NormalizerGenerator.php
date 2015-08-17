<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Model\JsonSchema;

use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

class NormalizerGenerator implements GeneratorInterface
{
    /**
     * @var TypeDecisionManager
     */
    private $typeDecisionManager;

    public function __construct(TypeDecisionManager $typeDecisionManager)
    {
        $this->typeDecisionManager = $typeDecisionManager;
    }

    /**
     * Generate a set of files given a schema
     *
     * @param JsonSchema $schema Schema to generate from
     * @param string $className Class to generate
     * @param Context $context Context for generation
     *
     * @return File[]
     */
    public function generate($schema, $className, Context $context)
    {
        $this->typeDecisionManager->resolveType($schema)->generateNormalizer($schema, $className, $context);

        $factory              = new BuilderFactory();
        $normalizerChainClass = $factory->class('NormalizerChain')
            ->implement('DenormalizerInterface')
            ->addStmt(
                $factory->property('normalizers')->makePrivate()->setDefault(new Expr\Array_())
            )
            ->addStmt(
                $factory->method('addNormalizer')
                    ->makePublic()
                    ->addParam($factory->param('normalizer'))
                    ->addStmt(
                        new Expr\MethodCall(new Expr\Variable('normalizer'), 'setNormalizerChain', [
                            new Arg(new Expr\Variable('this'))
                        ])
                    )
                    ->addStmt(
                        new Expr\Assign(new Expr\ArrayDimFetch(new Expr\PropertyFetch(new Expr\Variable('this'), 'normalizers')), new Expr\Variable('normalizer'))
                    )
            )
            ->addStmt(
                $factory->method('denormalize')
                    ->makePublic()
                    ->addParam($factory->param('data'))
                    ->addParam($factory->param('class'))
                    ->addParam($factory->param('format')->setDefault(null))
                    ->addParam($factory->param('context')->setTypeHint('array')->setDefault([]))
                    ->addStmt(
                        new Stmt\Foreach_(
                            new Expr\PropertyFetch(new Expr\Variable('this'), 'normalizers'),
                            new Expr\Variable('normalizer'),
                            [
                                'stmts' => [
                                    new Stmt\If_(
                                        new Expr\MethodCall(new Expr\Variable('normalizer'), 'supportsDenormalization', [
                                            new Arg(new Expr\Variable('data')),
                                            new Arg(new Expr\Variable('class')),
                                            new Arg(new Expr\Variable('format')),
                                        ]),
                                        [
                                            'stmts' => [
                                                new Stmt\Return_(new Expr\MethodCall(new Expr\Variable('normalizer'), 'denormalize', [
                                                    new Arg(new Expr\Variable('data')),
                                                    new Arg(new Expr\Variable('class')),
                                                    new Arg(new Expr\Variable('format')),
                                                    new Arg(new Expr\Variable('context')),
                                                ]))
                                            ]
                                        ]
                                    )
                                ]
                            ]
                        )
                    )
                    ->addStmt(new Stmt\Return_(new Expr\ConstFetch(new Name("null"))))
            )
            ->addStmt(
                $factory->method('supportsDenormalization')
                    ->makePublic()
                    ->addParam($factory->param('data'))
                    ->addParam($factory->param('type'))
                    ->addParam($factory->param('format')->setDefault(null))
                    ->addStmt(
                        new Stmt\Foreach_(
                            new Expr\PropertyFetch(new Expr\Variable('this'), 'normalizers'),
                            new Expr\Variable('normalizer'),
                            [
                                'stmts' => [
                                    new Stmt\If_(
                                        new Expr\MethodCall(new Expr\Variable('normalizer'), 'supportsDenormalization', [
                                            new Arg(new Expr\Variable('data')),
                                            new Arg(new Expr\Variable('type')),
                                            new Arg(new Expr\Variable('format')),
                                        ]),
                                        [
                                            'stmts' => [
                                                new Stmt\Return_(new Expr\ConstFetch(new Name("true")))
                                            ]
                                        ]
                                    )
                                ]
                            ]
                        )
                    )
                    ->addStmt(new Stmt\Return_(new Expr\ConstFetch(new Name("false"))))
            )
        ;

        $buildMethod = $factory->method('build')
            ->makeStatic()
            ->makePublic()
            ->addStmt(
                new Expr\Assign(new Expr\Variable('normalizer'), new Expr\New_(new Expr\ConstFetch(new Name("self"))))
            )
        ;

        foreach ($context->getFiles() as $file) {
            if (preg_match('/Normalizer/', $file->getFilename())) {
                $buildMethod->addStmt(
                    new Expr\MethodCall(new Expr\Variable('normalizer'), 'addNormalizer', [
                        new Arg(new Expr\New_(new Name($file->getNode()->stmts[2]->name)))
                    ])
                );
            }
        }

        $buildMethod->addStmt(
            new Stmt\Return_(new Expr\Variable('normalizer'))
        );

        $normalizerChainClass->addStmt($buildMethod);

        $node = $factory->namespace($context->getNamespace() . "\\Normalizer")
            ->addStmt($factory->use('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'))
            ->addStmt($normalizerChainClass)
            ->getNode()
        ;

        $context->addFile(new File($context->getDirectory() . DIRECTORY_SEPARATOR . 'Normalizer' . DIRECTORY_SEPARATOR . 'NormalizerChain.php', $node));

        return $context->getFiles();
    }
}
