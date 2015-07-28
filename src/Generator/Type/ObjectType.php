<?php

namespace Joli\Jane\Generator\Type;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;
use Memio\Model\Argument;
use Memio\Model\Contract;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Property;

class ObjectType extends AbstractType
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
        if ($schema->getDefinitions() !== null) {
            foreach ($schema->getDefinitions() as $key => $definition) {
                $this->typeDecisionManager->resolveType($definition)->generateObject($definition, $key, $context);
            }
        }

        $object = Object::make($context->getNamespace() . "\\Model\\". ucfirst($name));
        $context->getSchemaObjectMap()->addSchemaObject($schema, $object);

        if ($schema->getProperties() !== null) {
            foreach ($schema->getProperties() as $key => $property) {
                $subType = $this->typeDecisionManager->resolveType($property);
                $propGenerated = $subType->generateProperty($property, $key, $context);

                if ($propGenerated instanceof Property) {
                    $object->addProperty($propGenerated);
                }

                foreach ($subType->generateMethods($property, $key, $context) as $method) {
                    $object->addMethod($method);
                }
            }
        }

        if ($schema->getAdditionalProperties() || is_array($schema->getPatternProperties())) {
            $object->extend(new Object('\\ArrayObject'));
        }

        $schemaFile = File::make($context->getDirectory() . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . ucfirst($name) . '.php');
        $schemaFile->setStructure($object);

        if ($object->hasParent()) {
            $schemaFile->addFullyQualifiedName(FullyQualifiedName::make($object->getParent()->getFullyQualifiedName()));
        }

        $context->addFile($schemaFile);
    }

    /**
     * {@inheritDoc}
     */
    public function generateNormalizer($schema, $name, Context $context)
    {
        $object = Object::make($context->getNamespace() . "\\Normalizer\\". ucfirst($name).'Normalizer');
        $context->getSchemaObjectNormalizerMap()->addSchemaObject($schema, $object);
        $object->implement(Contract::make('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'));
        $object->addProperty(Property::make('normalizerChain'));
        $object->addMethod(
            Method::make('setNormalizerChain')
                ->addArgument(Argument::make($context->getNamespace() . "\\Normalizer\\NormalizerChain", 'normalizerChain'))
                ->setBody('        $this->normalizerChain = $normalizerChain;')
        );

        $denormalizeMethod = Method::make('denormalize')
            ->addArgument(
                Argument::make('mixed', 'data')
            )
            ->addArgument(
                Argument::make('string', 'class')
            )
            ->addArgument(
                Argument::make('string', 'format')
                    ->setDefaultValue('null')
            )
            ->addArgument(
                Argument::make('array', 'context')
                    ->setDefaultValue('array()')
            )
        ;

        $supportDenormalizationMethod = Method::make('supportsDenormalization')
            ->addArgument(
                Argument::make('mixed', 'data')
            )
            ->addArgument(
                Argument::make('string', 'type')
            )
            ->addArgument(
                Argument::make('string', 'format')
                    ->setDefaultValue('null')
            )
            ->setBody(sprintf(<<<EOC
        if (\$type !== '%s\\Model\\%s') {
            return false;
        }

        if (\$format !== 'json') {
            return false;
        }

        return true;
EOC
            , $context->getNamespace(), $name))
        ;

        $object->addMethod($denormalizeMethod);
        $object->addMethod($supportDenormalizationMethod);
        $lines    = [
            sprintf(<<<EOC
        if (empty(\$data)) {
            return null;
        }

        if (isset(\$data->{'\$ref'})) {
            return new Reference(\$data->{'\$ref'}, \$context['rootSchema'] ?: null);
        }

        \$object = new \\%s\\Model\\%s();

        if (!isset(\$context['rootSchema'])) {
            \$context['rootSchema'] = \$object;
        }

EOC
            , $context->getNamespace(), ucfirst($name))
        ];

        foreach ($schema->getProperties() as $key => $property) {
            $subType  = $this->typeDecisionManager->resolveType($property);

            $lines[] = sprintf(<<<EOC
        if (isset(\$data->{'%s'})) {
            %s
        }

EOC
            , $key, $subType->generateDenormalizationLine($property, $key, $context));
        }

        if ($schema->getPatternProperties()) {
        }

        $lines[] = sprintf(<<<EOC
        return \$object;
EOC
        );

        $denormalizeMethod->setBody(implode("\n", $lines));

        $schemaFile = File::make($context->getDirectory() . DIRECTORY_SEPARATOR . 'Normalizer' . DIRECTORY_SEPARATOR . ucfirst($name) . 'Normalizer.php');
        $schemaFile->setStructure($object);
        $schemaFile->addFullyQualifiedName(FullyQualifiedName::make('Joli\Jane\Reference\Reference'));
        $schemaFile->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'));
        $schemaFile->addFullyQualifiedName(FullyQualifiedName::make($context->getNamespace() . "\\Normalizer\\NormalizerChain"));

        $context->addFile($schemaFile);
    }

    /**
     * {@inheritDoc}
     */
    public function generateDenormalizationLine($schema, $name, Context $context, $mode = self::SET_OBJECT)
    {
        if (!$context->getSchemaObjectNormalizerMap()->hasSchema($schema)) {
            $this->typeDecisionManager->resolveType($schema)->generateNormalizer($schema, $name, $context);
        }

        return parent::generateDenormalizationLine($schema, $name, $context, $mode);
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
    public function supportSchema($schema)
    {
        return ($schema instanceof JsonSchema && $schema->getType() === 'object');
    }

    /**
     * {@inheritDoc}
     */
    public function getPhpTypes($schema, $name, Context $context)
    {
        return ["\\". $context->getNamespace()."\\".ucfirst($name)];
    }
}
