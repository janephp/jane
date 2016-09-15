<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\Model\ClassGenerator;
use Joli\Jane\Generator\Model\GetterSetterGenerator;
use Joli\Jane\Generator\Model\InterfaceGenerator;
use Joli\Jane\Generator\Model\PropertyGenerator;
use Joli\Jane\Model\JsonSchema;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class ModelGenerator implements GeneratorInterface
{
    use ClassGenerator;
    use GetterSetterGenerator;
    use PropertyGenerator;
    use InterfaceGenerator;

    const FILE_TYPE_MODEL = 'model';

    /**
     * @var Naming Naming Service
     */
    protected $naming;

    /**
     * @param Naming $naming Naming Service
     */
    public function __construct(Naming $naming)
    {
        $this->naming = $naming;
    }

    /**
     * Generate a model given a schema
     *
     * @param mixed   $schema    Schema to generate from
     * @param string  $className Class to generate
     * @param Context $context   Context for generation
     *
     * @return File[]
     */
    public function generate($schema, $className, Context $context)
    {
        $files = [];

        foreach ($context->getObjectClassMap() as $class) {
            $properties = [];
            $methods    = [];

            foreach ($class->getProperties() as $property) {
                $properties[] = $this->createProperty($property->getName(), $property->getType());
                $methods[]    = $this->createGetter($property->getName(), $property->getType());
                $methods[]    = $this->createSetter($property->getName(), $property->getType());
            }

            $model = $this->createModel(
                $class->getName(),
                $properties,
                $methods,
                $class->getTypes()
            );

            $namespace = new Stmt\Namespace_(new Name($context->getNamespace() . "\\Model"), [$model]);

            $files[] = new File(
                $context->getDirectory() . '/Model/' . $class->getName() . '.php', $namespace, self::FILE_TYPE_MODEL
            );

            $modelInterface = $this->createModelInterface($model->name, $methods);
            $namespace      = new Stmt\Namespace_(new Name($context->getNamespace() . "\\Model"), [$modelInterface]);

            $files[] = new File(
                $context->getDirectory() . '/Model/' . $modelInterface->name . '.php', $namespace, self::FILE_TYPE_MODEL
            );
        }

        return $files;
    }

    /**
     * The naming service
     *
     * @return Naming
     */
    protected function getNaming()
    {
        return $this->naming;
    }
}
