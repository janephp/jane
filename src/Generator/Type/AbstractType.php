<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;

abstract class AbstractType implements TypeInterface
{
    /**
     * Encode property name
     *
     * @param string $name
     *
     * @return string
     */
    protected function encodePropertyName($name)
    {
        if (preg_match('/\$/', $name)) {
            $name = preg_replace_callback('/\$([a-z])/', function ($matches) {
                return 'dollar'.ucfirst($matches[1]);
            }, $name);
        }

        return lcfirst($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty($schema, $name, Context $context)
    {
        $factory = new BuilderFactory();

        return $factory->property($this->encodePropertyName($name))->makeProtected()->setDocComment(
            new Doc(sprintf(<<<EOD
/**
 * @var %s
 */
EOD
            , $this->getPhpTypeAsString($schema, $name, $context)))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods($schema, $name, Context $context)
    {
        $factory      = new BuilderFactory();
        $propertyName = $this->encodePropertyName($name);
        $getter       = $factory->method('get'.ucfirst($propertyName))
            ->makePublic()
            ->setDocComment(new Doc(sprintf(<<<EOD
/**
 * @return %s
 */
EOD
                , $this->getPhpTypeAsString($schema, $name, $context))))
            ->addStmt(new Stmt\Return_(
                new Expr\PropertyFetch(new Expr\Variable('this'), $propertyName))
            )
        ;

        $setter       = $factory->method('set'.ucfirst($propertyName))
            ->makePublic()
            ->setDocComment(new Doc(sprintf(<<<EOD
/**
 * @param %s %s
 *
 * @return self
 */
EOD
                , $this->getPhpTypeAsString($schema, $name, $context), '$'.$propertyName)))
            ->addParam($factory->param($propertyName)->setDefault(null))
            ->addStmt(
                new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), $propertyName), new Expr\Variable($propertyName))
            )
            ->addStmt(new Stmt\Return_(new Expr\Variable('this')))
        ;

        return [$getter, $setter];
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationStmt($schema, $name, Context $context, Expr $input)
    {
        return [[], $this->getDenormalizationValueStmt($schema, $name, $context, $input)];
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationValueStmt($schema, $name, Context $context, Expr $input)
    {
        return $input;
    }

    /**
     * Get all the php types on one line
     *
     * @param Schema|Reference $schema
     * @param string           $name
     * @param Context          $context
     *
     * @return string
     */
    public function getPhpTypeAsString($schema, $name, Context $context)
    {
        return implode('|', $this->getPhpTypes($schema, $name, $context));
    }

    /**
     * @param Schema|Reference $schema
     * @param string           $name
     * @param Context          $context
     *
     * @return string
     */
    protected function getArgumentType($schema, $name, Context $context)
    {
        $types = $this->getPhpTypes($schema, $name, $context);
        $type  = 'mixed';

        if (count($types) == 1) {
            $type = $types[0];
        }

        if ($type == 'float') {
            $type = 'mixed';
        }

        if (preg_match('/\[\]/', $type)) {
            $type = 'mixed';
        }

        return $type;
    }

    protected function getAssignStatement(Expr $input, Expr $output)
    {

    }
}
