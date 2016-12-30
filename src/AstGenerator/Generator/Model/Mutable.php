<?php

declare(strict_types=1);

namespace Joli\Jane\AstGenerator\Generator\Model;

use Joli\Jane\AstGenerator\Naming;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;

class Mutable extends Model
{
    /**
     * {@inheritdoc}
     */
    public function createSetter($class, $property)
    {
        $typeHint = null;
        $propertyName = Naming::getPropertyName($property);
        $types = $this->propertyInfoExtractor->getTypes($class, $property);

        if (count($types) === 1 && $types[0]->getClassName() !== null && !$types[0]->isCollection()) {
            $typeHint = $types[0]->getClassName();
        }

        return new Stmt\ClassMethod(
            Naming::getPrefixedMethodName('set', $property),
            [
                'type' => Stmt\Class_::MODIFIER_PUBLIC,
                'params' => [
                    new Param($propertyName, new Expr\ConstFetch(new Name('null')), $typeHint),
                ],
                'stmts' => [
                    new Expr\Assign(
                        new Expr\PropertyFetch(
                            new Expr\Variable('this'),
                            $propertyName
                        ),
                        new Expr\Variable($propertyName)
                    ),
                    new Stmt\Return_(new Expr\Variable('this')),
                ],
            ],
            [
                'comments' => [new Doc(sprintf(<<<'EOD'
/**
 * @param %s $%s
 *
 * @return self
 */
EOD
                    , $this->getDocType($class, $property), $propertyName))],
            ]
        );
    }
}
