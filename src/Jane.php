<?php

namespace Joli\Jane;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\ModelGenerator;
use Joli\Jane\Generator\NormalizerGenerator;
use Joli\Jane\Generator\TypeDecisionManager;
use Joli\Jane\Model\JsonSchema;
use Joli\Jane\Normalizer\NormalizerChain;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\CS\Config\Config;
use Symfony\CS\ConfigurationResolver;
use Symfony\CS\Fixer;

class Jane
{
    private $serializer;

    private $modelGenerator;

    private $normalizerGenerator;

    private $fixer;

    public function __construct(Serializer $serializer, ModelGenerator $modelGenerator, NormalizerGenerator $normalizerGenerator, Fixer $fixer = null)
    {
        $this->serializer          = $serializer;
        $this->modelGenerator      = $modelGenerator;
        $this->normalizerGenerator = $normalizerGenerator;
        $this->fixer               = $fixer;
    }


    public function generate($schemaFilePath, $name, $namespace, $directory)
    {
        $schema  = $this->serializer->deserialize(file_get_contents($schemaFilePath), JsonSchema::class, 'json');
        $context = new Context($schema, $namespace, $directory);

        $modelFiles      = $this->modelGenerator->generate($schema, $name, $context);
        $normalizerFiles = $this->normalizerGenerator->generate($schema, $name, $context);
        $prettyPrinter   = new Standard();

        if (!file_exists(($directory . DIRECTORY_SEPARATOR . 'Model'))) {
            mkdir($directory . DIRECTORY_SEPARATOR . 'Model', 0755, true);
        }

        if (!file_exists(($directory . DIRECTORY_SEPARATOR . 'Normalizer'))) {
            mkdir($directory . DIRECTORY_SEPARATOR . 'Normalizer', 0755, true);
        }

        foreach ($modelFiles as $file) {
            file_put_contents($file->getFilename(), $prettyPrinter->prettyPrintFile([$file->getNode()]));
        }

        foreach ($normalizerFiles as $file) {
            file_put_contents($file->getFilename(), $prettyPrinter->prettyPrintFile([$file->getNode()]));
        }

        if ($this->fixer !== null) {
            $config = new Config();
            $config->setDir($directory);

            $resolver = new ConfigurationResolver();
            $resolver
                ->setAllFixers($this->fixer->getFixers())
                ->setConfig($config)
                ->setOptions(array(
                    'level' => 'symfony'
                ))
                ->resolve();

            $config->fixers($resolver->getFixers());

            $this->fixer->fix($config);
        }
    }

    public static function build()
    {
        $encoders       = [new JsonEncoder(new JsonEncode(), new JsonDecode(false))];
        $normalizers    = [NormalizerChain::build()];
        $serializer     = new Serializer($normalizers, $encoders);
        $typeDecision   = TypeDecisionManager::build($serializer);
        $modelGenerator = new ModelGenerator($typeDecision);
        $normGenerator  = new NormalizerGenerator($typeDecision);
        $fixer          = new Fixer();
        $fixer->registerBuiltInFixers();

        return new self($serializer, $modelGenerator, $normGenerator, $fixer);
    }
}
