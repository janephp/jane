<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Model\JsonSchema;
use Memio\Model\Argument;
use Memio\Model\Contract;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Property;

class NormalizerGenerator implements GeneratorInterface
{
    /**
     * @var TypeDecisionManager
     */
    private $typeDecisionManager;

    public function __construct(TypeDecisionManager $typeDecisionManager)
    {
        $this->typeDecisionManager = $typeDecisionManager;
    }

    /**
     * Generate a set of files given a schema
     *
     * @param JsonSchema $schema Schema to generate from
     * @param string $className Class to generate
     * @param Context $context Context for generation
     *
     * @return \Memio\Model\File[]
     */
    public function generate(JsonSchema $schema, $className, Context $context)
    {
        $this->typeDecisionManager->resolveType($schema)->generateNormalizer($schema, $className, $context);

        $object = Object::make($context->getNamespace() . "\\Normalizer\\NormalizerChain");
        $object->implement(Contract::make('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'));
        $object->addProperty(Property::make('normalizers')->setDefaultValue('[]'));
        $object->addMethod(
            Method::make('addNormalizer')
                ->addArgument(Argument::make('mixed', 'normalizer'))
                ->setBody(sprintf(<<<EOC
        \$normalizer->setNormalizerChain(\$this);
        \$this->normalizers[] = \$normalizer;
EOC
            ))
        );

        $object->addMethod(Method::make('denormalize')
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
            ->setBody(sprintf(<<<EOC
        foreach (\$this->normalizers as \$normalizer) {
            if (\$normalizer->supportsDenormalization(\$data, \$class, \$format)) {
                return \$normalizer->denormalize(\$data, \$class, \$format, \$context);
            }
        }

        return null;
EOC
            ))
        );

        $object->addMethod(Method::make('supportsDenormalization')
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
        foreach (\$this->normalizers as \$normalizer) {
            if (\$normalizer->supportsDenormalization(\$data, \$type, \$format)) {
                return true;
            }
        }

        return false;
EOC
            ))
        );

        $buildLines = [sprintf(<<<EOC
        \$normalizer = new self();

EOC
        )];

        foreach ($context->getFiles() as $file) {
            if (preg_match('/Normalizer/', $file->getStructure()->getFullyQualifiedName())) {
                $buildLines[] = sprintf(<<<EOC
        \$normalizer->addNormalizer(new %s());
EOC
                    , $file->getStructure()->getName());
            }
        }

        $buildLines[] = sprintf(<<<EOC

        return \$normalizer;
EOC
        );

        $object->addMethod(Method::make('build')
                ->makeStatic()
                ->setBody(implode("\n", $buildLines))
        );

        $schemaFile = File::make($context->getDirectory() . DIRECTORY_SEPARATOR . 'Normalizer' . DIRECTORY_SEPARATOR . 'NormalizerChain.php');
        $schemaFile->setStructure($object);
        $schemaFile->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'));

        $context->addFile($schemaFile);

        return $context->getFiles();
    }
}
