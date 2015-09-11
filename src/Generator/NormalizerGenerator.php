<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\Normalizer\DenormalizerGenerator;
use Joli\Jane\Generator\Normalizer\NormalizerGenerator as NormalizerGeneratorTrait;

use Joli\Jane\Model\JsonSchema;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

class NormalizerGenerator implements GeneratorInterface
{
    const FILE_TYPE_NORMALIZER = 'normalizer';

    use DenormalizerGenerator;
    use NormalizerGeneratorTrait;

    /**
     * @var \Joli\Jane\Generator\Naming
     */
    protected $naming;

    public function __construct(Naming $naming)
    {
        $this->naming = $naming;
    }

    /**
     * Generate a set of files given a schema
     *
     * @param mixed   $schema    Schema to generate from
     * @param string  $className Class to generate
     * @param Context $context   Context for generation
     *
     * @return File[]
     */
    public function generate($schema, $className, Context $context)
    {
        $files   = [];

        foreach ($context->getObjectClassMap() as $class) {
            $methods   = [];
            $modelFqdn = $context->getNamespace()."\\Model\\".$class->getName();
            $methods[] = $this->createSupportsDenormalizationMethod($modelFqdn);
            $methods[] = $this->createDenormalizeMethod($modelFqdn, $context, $class->getProperties());

            $normalizerClass = $this->createNormalizerClass(
                $class->getName().'Normalizer',
                $methods
            );

            $namespace = new Stmt\Namespace_(new Name($context->getNamespace()."\\Normalizer"), [
                new Stmt\Use_([new Stmt\UseUse(new Name('Joli\Jane\Reference\Reference'))]),
                new Stmt\Use_([new Stmt\UseUse(new Name('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'))]),
                new Stmt\Use_([new Stmt\UseUse(new Name('Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer'))]),
                $normalizerClass
            ]);
            $files[]   = new File($context->getDirectory().'/Normalizer/'.$class->getName().'Normalizer.php', $namespace, self::FILE_TYPE_NORMALIZER);
        }

        return $files;
    }
}
