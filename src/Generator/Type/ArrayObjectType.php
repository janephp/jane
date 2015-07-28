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
        $patternProperties = $schema->getPatternProperties();

        if (($additionalProperties === null || $additionalProperties === false) && ($patternProperties === null || $patternProperties === false)) {
            return parent::generateDenormalizationLine($schema, $name, $context);
        }

        $propertyName = $this->encodePropertyName($name);

        $lines = [sprintf(<<<EOC
            \$values = new \\ArrayObject([], \\ArrayObject::ARRAY_AS_PROPS);

            foreach (\$data->{'%s'} as \$key => \$value) {
EOC
        , $name)];

        if (!empty($patternProperties)) {
            foreach ($patternProperties as $pattern => $schema) {
                $lines[] = sprintf(<<<EOC
                if (preg_match('/%s/', \$key)) {
                    %s
                    continue;
                }

EOC
                , str_replace('/', '\\/', $pattern), $this->typeDecisionManager->resolveType($schema)->generateDenormalizationLine($schema, $name, $context, TypeInterface::SET_ARRAY));
            }
        }

        if ($additionalProperties !== null && $additionalProperties !== false) {
            $lines[] = $this->typeDecisionManager->resolveType($additionalProperties)->generateDenormalizationLine($additionalProperties, $name, $context, TypeInterface::SET_ARRAY);
        }

        $lines[] = sprintf(<<<EOC
            }

            \$object->set%s(\$values);
EOC
            , ucfirst($propertyName)
        );

        return implode("\n", $lines);
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

        if ($schema->getAdditionalProperties() === false && ($schema->getPatternProperties() === false || $schema->getPatternProperties() === null)) {
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
