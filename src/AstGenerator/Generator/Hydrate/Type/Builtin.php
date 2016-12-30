<?php

declare(strict_types=1);

namespace Joli\Jane\AstGenerator\Generator\Hydrate\Type;

use Joli\Jane\AstGenerator\Generator\AstGeneratorInterface;
use Joli\Jane\AstGenerator\Generator\Exception\MissingContextException;
use Joli\Jane\AstGenerator\Generator\Exception\NotSupportedGeneratorException;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name;
use Symfony\Component\PropertyInfo\Type;

/**
 * Generate hydration of built in type.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class Builtin implements AstGeneratorInterface
{
    protected $supportedTypes = array(
        Type::BUILTIN_TYPE_BOOL,
        Type::BUILTIN_TYPE_FLOAT,
        Type::BUILTIN_TYPE_INT,
        Type::BUILTIN_TYPE_NULL,
        Type::BUILTIN_TYPE_STRING,
    );

    protected $conditionMapping = array(
        Type::BUILTIN_TYPE_BOOL => 'is_bool',
        Type::BUILTIN_TYPE_FLOAT => 'is_float',
        Type::BUILTIN_TYPE_INT => 'is_int',
        Type::BUILTIN_TYPE_NULL => 'is_null',
        Type::BUILTIN_TYPE_STRING => 'is_string',
    );

    /**
     * {@inheritdoc}
     *
     * @param Type $object A type extracted with PropertyInfo component
     */
    public function generate($object, array $context = array())
    {
        if (!($object instanceof Type) || !in_array($object->getBuiltinType(), $this->supportedTypes)) {
            throw new NotSupportedGeneratorException();
        }

        if (!isset($context['input']) || !($context['input'] instanceof Expr)) {
            throw new MissingContextException('Input variable not defined or not an Expr in generation context');
        }

        if (!isset($context['output']) || !($context['output'] instanceof Expr)) {
            throw new MissingContextException('Output variable not defined or not an Expr in generation context');
        }

        $assign = array(
            new Expr\Assign($context['output'], $context['input']),
        );

        if (isset($context['condition']) && $context['condition']) {
            return array(new Stmt\If_(
                new Expr\FuncCall(
                    new Name($this->conditionMapping[$object->getBuiltinType()]),
                    array(
                        new Arg($context['input']),
                    )
                ),
                array(
                    'stmts' => $assign,
                )
            ));
        }

        return $assign;
    }
}
