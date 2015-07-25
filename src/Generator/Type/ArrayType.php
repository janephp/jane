<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

class ArrayType extends AbstractType
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
        $items = $schema->getItems();

        if ($items === null) {
            return parent::generateDenormalizationLine($schema, $name, $context);
        }

        $propertyName = $this->encodePropertyName($name);

        if (is_array($items)) {
            $lines = [sprintf(<<<EOC
            \$values = [];

            foreach (\$data->{'%s'} as \$value) {
EOC
            , $name)];

            foreach ($items as $item) {
                $lines[] = sprintf(<<<EOC
                if (%s) {
                    \$values[] = %s;
                }

EOC
                , $this->typeDecisionManager->resolveType($item)->getRawCheck($item, $name, $context)
                , sprintf(
                    $this->typeDecisionManager->resolveType($item)->getDenormalizationValuePattern($item, $name, $context),
                    '$value'
                ));
            }

            $lines[] = sprintf(<<<EOC
            }

            \$object->set%s(\$values);

EOC
            , ucfirst($propertyName));

            return implode("\n", $lines);
        }

        return sprintf(<<<EOC
            \$values = [];

            foreach (\$data->{'%s'} as \$value) {
                \$values[] = %s;
            }

            \$object->set%s(\$values);

EOC
        , $name, sprintf(
            $this->typeDecisionManager->resolveType($items)->getDenormalizationValuePattern($items, $name, $context),
            '$value'
        ), ucfirst($propertyName));
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        return 'is_array(%s)';
    }

    /**
     * {@inheritDoc}
     */
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && $schema->getType() === 'array');
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $items = $schema->getItems();

        if ($items === null) {
            return ['array'];
        }

        if (is_array($items)) {
            $types = [];

            foreach ($items as $item) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($item)->getPhpTypes($item, $name, $context));
            }
        } else {
            $types = $this->typeDecisionManager->resolveType($items)->getPhpTypes($items, $name, $context);
        }

        $types = array_map(function ($type) {
            return $type.'[]';
        }, $types);

        return $types;
    }
}
