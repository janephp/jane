<?php

namespace Joli\Jane\Command;

use Joli\Jane\Generator\GeneratorConfig;
use Joli\Jane\Generator\SchemaConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

        $configuration = new GeneratorConfig();

        if (array_key_exists('json-schema-file', $options)) {
            $schema = $options['json-schema-file'];
            unset($options['json-schema-file']);

            $configuration->addSchemaConfig($this->resolveConfiguration($schema, $options));
        } else {
            foreach ($options as $schema => $schemaOptions) {
                $configuration->addSchemaConfig($this->resolveConfiguration($schema, $schemaOptions));
            }
        }

        $jane = \Joli\Jane\Jane::build();
        $files = $jane->generate($configuration);

        foreach ($files as $file) {
            $output->writeln(sprintf('Generated %s', $file));
        }
    }

    /**
     * @param       $schema
     * @param array $options
     *
     * @return SchemaConfig
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

        return new SchemaConfig($schema, $options['root-class'], $options['namespace'], $options['directory'], $options['reference'], $options['date-format']);
    }
}
