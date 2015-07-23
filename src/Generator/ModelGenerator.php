<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Schema\Schema;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Object;
use Memio\Model\Phpdoc\PropertyPhpdoc;
use Memio\Model\Phpdoc\VariableTag;
use Memio\Model\Property;

class ModelGenerator implements GeneratorInterface
{
    /**
     * @var TypeManager
     */
    private $typeManager;

    public function __construct(TypeManager $typeManager)
    {
        $this->typeManager = $typeManager;
    }

    /**
     * Generate a model given a schema
     *
     * @param Schema  $schema     Schema to generate from
     * @param string  $className  Class to generate
     * @param Context $context    Context for generation
     *
     * @return \Memio\Model\File[]
     */
    public function generate(Schema $schema, $className, Context $context)
    {
        $files = [];

        foreach ($schema->getDefinitions() as $key => $definition) {
            if ($definition instanceof Schema) {
                $files = array_merge($files, $this->generate($definition, ucfirst($key), $context));
            }
        }

        $object  = null;

        if ($this->typeManager->isObjectOfType($schema, 'object', $context)) {
            $object = $this->generateObject($schema, $className, $context);
        }

        if ($this->typeManager->isObjectOfType($schema, 'array', $context)) {
            $object = $this->generateArrayObject($schema, $className, $context);
        }

        if ($object !== null) {
            $schemaFile = File::make($context->getDirectory() . DIRECTORY_SEPARATOR . $className . '.php');
            $schemaFile->setStructure($object);

            if ($object->hasParent()) {
                $schemaFile->addFullyQualifiedName(FullyQualifiedName::make($object->getParent()->getFullyQualifiedName()));
            }

            $files[] = $schemaFile;
        }

        return $files;
    }

    /**
     * Generate a object model given a schema
     *
     * @param Schema  $schema     Schema to generate from
     * @param string  $className  Class to generate
     * @param Context $context    Context for generation
     *
     * @return \Memio\Model\Object
     */
    public function generateObject(Schema $schema, $className, Context $context)
    {
        $class = Object::make($context->getNamespace() . "\\". $className);
        $context->getSchemaObjectMap()->addSchemaObject($schema, $class);

        foreach ($schema->getProperties() as $key => $property) {
            if (preg_match('/\$/', $key)) {
                $key = preg_replace_callback('/\$([a-z])/', function ($matches) {
                    return 'dollar'.ucfirst($matches[1]);
                }, $key);
            }

            $phpDoc = new PropertyPhpdoc();
            $phpDoc->setVariableTag(VariableTag::make($this->typeManager->getVariableTagString($property, $key, $context)));

            $prop = new Property($key);
            $prop->makeProtected();
            $prop->setPhpdoc($phpDoc);

            $class->addProperty($prop);
        }

        if ($schema->getAdditionalProperties()) {
            $class->extend(new Object('\\ArrayObject'));
        }

        return $class;
    }

    /**
     * Generate a array object model given a schema
     *
     * @param Schema  $schema     Schema to generate from
     * @param string  $className  Class to generate
     * @param Context $context    Context for generation
     *
     * @return \Memio\Model\Object
     */
    public function generateArrayObject(Schema $schema, $className, Context $context)
    {
        $class  = Object::make($context->getNamespace() . "\\". $className);
        $context->getSchemaObjectMap()->addSchemaObject($schema, $class);
        $class->extend(new Object('ArrayObject'));

        return $class;
    }
}
