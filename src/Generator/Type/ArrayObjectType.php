<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

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
    public function generateDenormalizationLine($schema, $name, Context $context, $mode = self::SET_OBJECT)
    {
        $additionalProperties = $schema->getAdditionalProperties();

        if ($additionalProperties === null || $additionalProperties === true) {
            return parent::generateDenormalizationLine($schema, $name, $context);
        }

        $propertyName = $this->encodePropertyName($name);

        return sprintf(<<<EOC
            \$values = new \\ArrayObject([], \\ArrayObject::ARRAY_AS_PROPS);

            foreach (\$data->{'%s'} as \$key => \$value) {
                %s
            }

            \$object->set%s(\$values);
EOC
            , $name
            , $this->typeDecisionManager->resolveType($additionalProperties)->generateDenormalizationLine($additionalProperties, $name, $context, TypeInterface::SET_ARRAY)
            , ucfirst($propertyName)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        if (!($schema instanceof JsonSchema)) {
            return false;
        }

        if ($schema->getType() !== 'object') {
            return false;
        }

        if (!empty($schema->getProperties())) {
            return false;
        }

        if ($schema->getAdditionalProperties() === false) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        return 'is_object(%s)';
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $additionalProperties = $schema->getAdditionalProperties();

        if ($additionalProperties === true || $additionalProperties === null) {
            return ['array'];
        }

        $types = $this->typeDecisionManager->resolveType($additionalProperties)->getPhpTypes($additionalProperties, $name, $context);
        $types = array_map(function ($type) {
            return $type.'[]';
        }, $types);

        return $types;
    }

}
 