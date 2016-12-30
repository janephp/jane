<?php

namespace Joli\Jane\AstGenerator\Generator\Normalizer;

use Joli\Jane\AstGenerator\Generator\AstGeneratorInterface;
use Joli\Jane\AstGenerator\UniqueVariableScope;
use Joli\Jane\AstGenerator\Generator\Exception\NotSupportedGeneratorException;
use PhpParser\Comment;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

/**
 * Generate a Normalizer given a Class.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class Normalizer implements AstGeneratorInterface
{
    /** @var AstGeneratorInterface AstGenerator which generate the statements for normalization of a given class */
    protected $normalizeStatementsGenerator;

    /** @var AstGeneratorInterface AstGenerator which generate the statements for denormalization of a given class */
    protected $denormalizeStatementsGenerator;

    /** @var PropertyInfoExtractorInterface */
    private $propertyInfoExtractor;

    /**
     * NormalizerGenerator constructor.
     *
     * @param AstGeneratorInterface          $normalizeStatementsGenerator   AstGenerator which generate the statements for normalization of a given class
     * @param AstGeneratorInterface          $denormalizeStatementsGenerator AstGenerator which generate the statements for denormalization of a given class
     * @param PropertyInfoExtractorInterface $propertyInfoExtractor
     */
    public function __construct(AstGeneratorInterface $normalizeStatementsGenerator, AstGeneratorInterface $denormalizeStatementsGenerator, PropertyInfoExtractorInterface $propertyInfoExtractor)
    {
        $this->normalizeStatementsGenerator = $normalizeStatementsGenerator;
        $this->denormalizeStatementsGenerator = $denormalizeStatementsGenerator;
        $this->propertyInfoExtractor = $propertyInfoExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, array $context = array())
    {
        if (!is_string($object) || count($this->propertyInfoExtractor->getProperties($object)) === 0) {
            throw new NotSupportedGeneratorException();
        }

        if (!isset($context['short_class_name'])) {
            $reflectionClass = new \ReflectionClass($object);
            $context['short_class_name'] = $reflectionClass->getShortName().'Normalizer';
        }

        return array(new Stmt\Class_(
            new Name($context['short_class_name']),
            array(
                'stmts' => array(
                    new Stmt\Use_(array(
                        new Name('\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait'),
                        new Name('\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait'),
                    )),
                    $this->createSupportsNormalizationMethod($object),
                    $this->createSupportsDenormalizationMethod($object),
                    $this->createNormalizeMethod($object, array_merge($context, array(
                        'unique_variable_scope' => new UniqueVariableScope(),
                    ))),
                    $this->createDenormalizeMethod($object, array_merge($context, array(
                        'unique_variable_scope' => new UniqueVariableScope(),
                    ))),
                ),
                'implements' => array(
                    new Name('\Symfony\Component\Serializer\Normalizer\DenormalizerInterface'),
                    new Name('\Symfony\Component\Serializer\Normalizer\NormalizerInterface'),
                    new Name('\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface'),
                    new Name('\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface'),
                ),
            ),
            array(
                'comments' => array(new Comment("/**\n * This class is generated.\n * Please do not update it manually.\n */")),
            )
        ));
    }

    /**
     * Create method to check if normalization is supported.
     *
     * @param string $class Fully Qualified name of the model class
     *
     * @return Stmt\ClassMethod
     */
    protected function createSupportsNormalizationMethod($class)
    {
        if (strpos($class, '\\') !== 0) {
            $class = '\\'.$class;
        }

        return new Stmt\ClassMethod('supportsNormalization', array(
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => array(
                new Param('data'),
                new Param('format', new Expr\ConstFetch(new Name('null'))),
            ),
            'stmts' => array(
                new Stmt\If_(
                    new Expr\Instanceof_(new Expr\Variable('data'), new Name($class)),
                    array(
                        'stmts' => array(
                            new Stmt\Return_(new Expr\ConstFetch(new Name('true'))),
                        ),
                    )
                ),
                new Stmt\Return_(new Expr\ConstFetch(new Name('false'))),
            ),
        ));
    }

    /**
     * Create method to check if denormalization is supported.
     *
     * @param string $class Fully Qualified name of the model class
     *
     * @return Stmt\ClassMethod
     */
    protected function createSupportsDenormalizationMethod($class)
    {
        return new Stmt\ClassMethod('supportsDenormalization', array(
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => array(
                new Param('data'),
                new Param('type'),
                new Param('format', new Expr\ConstFetch(new Name('null'))),
            ),
            'stmts' => array(
                new Stmt\If_(
                    new Expr\BinaryOp\NotIdentical(new Expr\Variable('type'), new Scalar\String_($class)),
                    array(
                        'stmts' => array(
                            new Stmt\Return_(new Expr\ConstFetch(new Name('false'))),
                        ),
                    )
                ),
                new Stmt\Return_(new Expr\ConstFetch(new Name('true'))),
            ),
        ));
    }

    /**
     * Create the normalization method.
     *
     * @param string $class   Class to create normalization from
     * @param array  $context Context of generation
     *
     * @throws NotSupportedGeneratorException
     *
     * @return Stmt\ClassMethod
     */
    protected function createNormalizeMethod($class, array $context = array())
    {
        $input = new Expr\Variable('object');
        $output = new Expr\Variable('data');

        return new Stmt\ClassMethod('normalize', array(
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => array(
                new Param('object'),
                new Param('format', new Expr\ConstFetch(new Name('null'))),
                new Param('context', new Expr\Array_(), 'array'),
            ),
            'stmts' => array_merge($this->normalizeStatementsGenerator->generate($class, array_merge($context, array(
                'input' => $input,
                'output' => $output,
                'normalizer' => new Expr\PropertyFetch(
                    new Expr\Variable('this'),
                    'normalizer'
                ),
                'format' => new Expr\Variable(new Name('format')),
                'context' => new Expr\Variable(new Name('context')),
            ))), array(
                new Stmt\Return_($output),
            )),
        ));
    }

    /**
     * Create the denormalization method.
     *
     * @param string $class   Class to create denormalization from
     * @param array  $context Context of generation
     *
     * @throws NotSupportedGeneratorException
     *
     * @return Stmt\ClassMethod
     */
    protected function createDenormalizeMethod($class, array $context = array())
    {
        $input = new Expr\Variable('data');
        $output = new Expr\Variable('object');

        return new Stmt\ClassMethod('denormalize', array(
            'type' => Stmt\Class_::MODIFIER_PUBLIC,
            'params' => array(
                new Param('data'),
                new Param('class'),
                new Param('format', new Expr\ConstFetch(new Name('null'))),
                new Param('context', new Expr\Array_(), 'array'),
            ),
            'stmts' => array_merge($this->denormalizeStatementsGenerator->generate($class, array_merge($context, array(
                'input' => $input,
                'output' => $output,
                'denormalizer' => new Expr\PropertyFetch(
                    new Expr\Variable('this'),
                    'denormalizer'
                ),
                'format' => new Expr\Variable(new Name('format')),
                'context' => new Expr\Variable(new Name('context')),
            ))), array(
                new Stmt\Return_($output),
            )),
        ));
    }
}
