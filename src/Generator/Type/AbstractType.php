<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Memio\Model\Argument;
use Memio\Model\Method;
use Memio\Model\Phpdoc\MethodPhpdoc;
use Memio\Model\Phpdoc\ParameterTag;
use Memio\Model\Phpdoc\PropertyPhpdoc;
use Memio\Model\Phpdoc\ReturnTag;
use Memio\Model\Phpdoc\VariableTag;
use Memio\Model\Property;

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

        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function generateProperty($schema, $name, Context $context)
    {
        $phpDoc = new PropertyPhpdoc();
        $phpDoc->setVariableTag(VariableTag::make($this->getPhpTypeAsString($schema, $name, $context)));

        $property = new Property($this->encodePropertyName($name));
        $property->makeProtected();
        $property->setPhpdoc($phpDoc);

        return $property;
    }

    /**
     * {@inheritDoc}
     */
    public function generateMethods($schema, $name, Context $context)
    {
        $propertyName = $this->encodePropertyName($name);

        $getterPhpdoc = MethodPhpdoc::make()
            ->setReturnTag(ReturnTag::make($this->getPhpTypeAsString($schema, $name, $context)));
        $getter = Method::make('get'.ucfirst($propertyName))
            ->setBody(
                sprintf('        return $this->%s;', $propertyName, $propertyName)
            )
            ->setPhpdoc($getterPhpdoc)
        ;

        $setterPhpdoc = MethodPhpdoc::make()
            ->addParameterTag(ParameterTag::make($this->getPhpTypeAsString($schema, $name, $context), $propertyName));
        $setter = Method::make('set'.ucfirst($propertyName))
            ->addArgument(Argument::make($this->getArgumentType($schema, $name, $context), $propertyName))
            ->setBody(
                sprintf('        $this->%s = $%s;', $propertyName, $propertyName)
            )
            ->setPhpdoc($setterPhpdoc)
        ;

        return [$getter, $setter];
    }

    /**
     * {@inheritDoc}
     */
    public function generateDenormalizationLine($schema, $name, Context $context, $mode = self::SET_OBJECT)
    {
        $propertyName = $this->encodePropertyName($name);

        if ($mode == TypeInterface::SET_OBJECT) {
            return sprintf(
                "\$object->set%s(%s);", ucfirst($propertyName), sprintf(
                    $this->getDenormalizationValuePattern($schema, $name, $context),
                    sprintf("\$data->{'%s'}", $name)
                )
            );
        }

        if ($name === null) {
            return sprintf(
                "\$values[] = %s;", sprintf(
                    $this->getDenormalizationValuePattern($schema, $name, $context),
                    sprintf("\$value", $name)
                )
            );
        }

        return sprintf(
            "\$values[\$key] = %s;", sprintf(
                $this->getDenormalizationValuePattern($schema, $name, $context),
                sprintf("\$value", $name)
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getDenormalizationValuePattern($schema, $name, Context $context)
    {
        return '%s';
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
}
