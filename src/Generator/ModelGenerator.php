<?php

namespace Joli\Jane\Generator;

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
     * @param Schema $rootSchema     Root schema to generate from
     * @param Schema $schema         Schema to generate from
     * @param string $modelName      Model class root name to generate
     * @param string $modelNamespace Namespace of model to generate
     * @param string $directory      Directory where files are generated
     *
     * @return \Memio\Model\File[]
     */
    public function generate(Schema $rootSchema, Schema $schema, $modelName, $modelNamespace, $directory)
    {
        $files = [];

        foreach ($schema->getDefinitions() as $key => $definition) {
            if ($definition instanceof Schema) {
                $files = array_merge($files, $this->generate($rootSchema, $definition, ucfirst($key), $modelNamespace, $directory));
            }
        }

        $object  = null;

        if ($this->typeManager->isObjectOfType($rootSchema, $schema, 'object')) {
            $object = $this->generateObject($rootSchema, $schema, $modelName, $modelNamespace, $directory);
        }

        if ($this->typeManager->isObjectOfType($rootSchema, $schema, 'array')) {
            $object = $this->generateArrayObject($rootSchema, $schema, $modelName, $modelNamespace, $directory);
        }

        if ($object !== null) {
            $schemaFile = File::make($directory . DIRECTORY_SEPARATOR . $modelName . '.php');
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
     * @param Schema $rootSchema     Root schema to generate from
     * @param Schema $schema         Schema to generate from
     * @param string $modelName      Model class root name to generate
     * @param string $modelNamespace Namespace of model to generate
     * @param string $directory      Directory where files are generated
     *
     * @return \Memio\Model\Object
     */
    public function generateObject(Schema $rootSchema, Schema $schema, $modelName, $modelNamespace, $directory)
    {
        $class = Object::make($modelNamespace . "\\". $modelName);

        foreach ($schema->getProperties() as $key => $property) {
            if (preg_match('/\$/', $key)) {
                $key = preg_replace_callback('/\$([a-z])/', function ($matches) {
                    return 'dollar'.ucfirst($matches[1]);
                }, $key);
            }

            $phpDoc = new PropertyPhpdoc();
            $phpDoc->setVariableTag(VariableTag::make($this->typeManager->getVariableTagString($schema, $property, $key, $modelNamespace)));

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
     * @param Schema $rootSchema     Root schema to generate from
     * @param Schema $schema         Schema to generate from
     * @param string $modelName      Model class root name to generate
     * @param string $modelNamespace Namespace of model to generate
     * @param string $directory      Directory where files are generated
     *
     * @return \Memio\Model\Object
     */
    public function generateArrayObject(Schema $rootSchema, Schema $schema, $modelName, $modelNamespace, $directory)
    {
        $class  = Object::make($modelNamespace . "\\". $modelName);
        $class->extend(new Object('ArrayObject'));

        return $class;
    }
}
