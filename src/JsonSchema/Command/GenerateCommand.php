<?php

namespace Joli\Jane\JsonSchema\Command;

use Joli\Jane\AstGenerator\Generator\Model\Mutable;
use Joli\Jane\AstGenerator\Generator\SchemaGenerator;
use Joli\Jane\AstGenerator\Writer\NamespaceWriter;
use Joli\Jane\JsonReference\ReferenceNormalizer;
use Joli\Jane\JsonSchema\Generator;
use Joli\Jane\JsonSchema\Guesser\JsonSchema\JsonSchemaGuesserFactory;
use Joli\Jane\JsonSchema\Normalizer\JsonSchemaNormalizer;
use Joli\Jane\JsonSchema\Registry\Builder;
use Joli\Jane\JsonSchema\Registry\Registry;
use Joli\Jane\JsonSchema\Registry\Schema;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class GenerateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate a set of class and normalizers given a specific Json Schema file');
        $this->addOption('config-file', 'c', InputOption::VALUE_REQUIRED, 'File to use for jane configuration', '.jane');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getOption('config-file');

        if (!file_exists($configFile)) {
            throw new \RuntimeException(sprintf('Config file %s does not exist', $configFile));
        }

        $options = require $configFile;

        if (!is_array($options)) {
            throw new \RuntimeException(sprintf('Invalid config file specified or invalid return type in file %s', $configFile));
        }

        $schemas = [];

        if (array_key_exists('json-schema-file', $options)) {
            $schema = $options['json-schema-file'];
            unset($options['json-schema-file']);

            $schemas[] = $this->resolveConfiguration($schema, $options);
        } else {
            foreach ($options as $schema => $schemaOptions) {
                $schemas[] = $this->resolveConfiguration($schema, $schemaOptions);
            }
        }

        $registry = $this->getRegistryBuilder()->build($schemas);

        $this->getGenerator($registry)->generate($schemas);
    }

    /**
     * @param       $schema
     * @param array $options
     *
     * @return Schema
     */
    protected function resolveConfiguration($schema, array $options = [])
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            'reference' => true,
            'date-format' => \DateTime::RFC3339,
        ]);

        $optionsResolver->setRequired([
            'root-class',
            'namespace',
            'directory',
        ]);

        $options = $optionsResolver->resolve($options);

        return new Schema($schema, $options['namespace'], $options['directory'], $options['root-class']);
    }

    /**
     * @return Builder
     */
    protected function getRegistryBuilder()
    {
        return new Builder($this->getGuesser(), $this->getSerializer());
    }

    /**
     * @param Registry $registry
     *
     * @return Generator
     */
    protected function getGenerator(Registry $registry)
    {
        $writer = new NamespaceWriter(new Standard());

        foreach ($registry->getSchemas() as $schema) {
            $writer->registerNamespace($schema->getNamespace(), $schema->getDirectory());
        }

        return new Generator($this->getSchemaGenerator($registry), $writer);
    }

    protected function getSerializer()
    {
        return new Serializer([
            new ReferenceNormalizer(),
            new JsonSchemaNormalizer(),
        ], [
            new JsonEncoder(new JsonEncode(), new JsonDecode())
        ]);
    }

    protected function getGuesser()
    {
        return JsonSchemaGuesserFactory::create($this->getSerializer());
    }

    protected function getSchemaGenerator(Registry $registry)
    {
        $generator = new SchemaGenerator($registry);
        $generator->setPopoGenerator($this->getPopoGenerator($registry));

        return $generator;
    }

    protected function getPopoGenerator(Registry $registry)
    {
        return new Mutable($registry);
    }
}
