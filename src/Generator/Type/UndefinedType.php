<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;

class UndefinedType extends AbstractType
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
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && $schema->getType() === null);
    }

    /**
     * {@inheritDoc}
     */
    public function generateDenormalizationLine($schema, $name, Context $context, $mode = self::SET_OBJECT)
    {
        if ($schema->getAnyOf() === null && $schema->getAllOf() === null && $schema->getOneOf() === null) {
            return parent::generateDenormalizationLine($schema, $name, $context, $mode);
        }

        $rawValue = $mode == self::SET_OBJECT ? sprintf("\$data->{'%s'}", $name) : '$value';
        $lines = [];

        if ($schema->getAnyOf() !== null) {
            foreach ($schema->getAnyOf() as $subSchema) {
                $lines[] = sprintf(<<<EOC
            if (%s) {
                %s
            }

EOC
                    , str_replace('%s', $rawValue, $this->typeDecisionManager->resolveType($subSchema)->getRawCheck($subSchema, $name, $context, $mode)), $this->typeDecisionManager->resolveType($subSchema)->generateDenormalizationLine($subSchema, $name, $context, $mode)
                );
            }
        }

        if ($schema->getAllOf() !== null) {
            foreach ($schema->getAllOf() as $subSchema) {
                $lines[] = sprintf(<<<EOC
            if (%s) {
                %s
            }

EOC
                    , str_replace('%s', $rawValue, $this->typeDecisionManager->resolveType($subSchema)->getRawCheck($subSchema, $name, $context, $mode)), $this->typeDecisionManager->resolveType($subSchema)->generateDenormalizationLine($subSchema, $name, $context, $mode)
                );
            }
        }

        if ($schema->getOneOf() !== null) {
            foreach ($schema->getOneOf() as $subSchema) {
                $lines[] = sprintf(<<<EOC
            if (%s) {
                %s
            }

EOC
                    , str_replace('%s', $rawValue, $this->typeDecisionManager->resolveType($subSchema)->getRawCheck($subSchema, $name, $context, $mode)), $this->typeDecisionManager->resolveType($subSchema)->generateDenormalizationLine($subSchema, $name, $context, $mode)
                );
            }
        }

        return implode("\n", $lines);
    }

    /**
     * {@inheritDoc}
     */
    public function getRawCheck($schema, $name, Context $context)
    {
        return 'isset(%s)';
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        $types = [];

        if ($schema->getAnyOf() !== null) {
            foreach ($schema->getAnyOf() as $subSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($subSchema)->getPhpTypes($subSchema, $name, $context));
            }
        }

        if ($schema->getAllOf() !== null) {
            foreach ($schema->getAllOf() as $subSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($subSchema)->getPhpTypes($subSchema, $name, $context));
            }
        }

        if ($schema->getOneOf() !== null) {
            foreach ($schema->getOneOf() as $subSchema) {
                $types = array_merge($types, $this->typeDecisionManager->resolveType($subSchema)->getPhpTypes($subSchema, $name, $context));
            }
        }

        if (empty($types)) {
            return ['mixed'];
        }

        return $types;
    }
}
