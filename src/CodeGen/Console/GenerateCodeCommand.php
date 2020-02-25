<?php

namespace Trellis\CodeGen\Console;

use Trellis\CodeGen\Service;
use Trellis\CodeGen\Parser\Config\ConfigIniParser;
use Trellis\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;
use Trellis\Common\Error\BadValueException;
use Trellis\Common\Error\NotReadableException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class GenerateCodeCommand extends Command
{
    protected static $generate_action_aliases = array('generate', 'gen', 'g');

    protected static $deploy_action_aliases = array('deploy', 'dep', 'd');

    protected $service;

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    protected function configure()
    {
        $this->setName('generate_code')
            ->setDescription('Generate and/or deploy code for a given type schema_path.')
            ->addOption(
                'config',
                'c',
                InputArgument::OPTIONAL,
                'Path pointing to a valid (ini) config file.'
            )
            ->addOption(
                'schema',
                's',
                InputArgument::OPTIONAL,
                'Path pointing to a valid (xml) type schema file.'
            )
            ->addOption(
                'directory',
                'd',
                InputArgument::OPTIONAL,
                'When the config or schema file are omitted, trellis will look for standard files in this directory.'
            )
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'Tell whether to generate and or deploy code. Valid values are `gen`, `dep` and `gen+dep`.',
                'gen+dep'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // define a new output style for warnings
        $output->getFormatter()->setStyle('warning', new OutputFormatterStyle('red', 'yellow'));

        $input_actions = $this->validateInputAction($input);
        $service = $this->getService($input, $output);
        $type_schema_path = $this->getEntityTypeSchemaPath($input);

        if (in_array('generate', $input_actions)) {
            $service->generate($type_schema_path);
        }
        if (in_array('deploy', $input_actions)) {
            $service->deploy($type_schema_path);
        }

        return 0;
    }

    protected function validateInputAction(InputInterface $input)
    {
        $sanitized_actions = [];

        $valid_actions = array_merge(self::$generate_action_aliases, self::$deploy_action_aliases);
        $input_actions = explode('+', $input->getArgument('action'));
        foreach ($input_actions as $input_action) {
            if (!in_array($input_action, $valid_actions)) {
                throw new BadValueException(
                    sprintf('The given `action` argument value `%s` is not supported.', $input_action)
                );
            }
        }

        $diff_count = count(array_diff(self::$generate_action_aliases, $input_actions));
        if ($diff_count < count(self::$generate_action_aliases)) {
            $sanitized_actions[] = 'generate';
        }

        $diff_count = count(array_diff(self::$deploy_action_aliases, $input_actions));
        if ($diff_count < count(self::$deploy_action_aliases)) {
            $sanitized_actions[] = 'deploy';
        }

        return $sanitized_actions;
    }

    protected function getService(InputInterface $input, OutputInterface $output)
    {
        if (!$this->service) {
            $this->service = new Service(
                array(
                    'config' => $this->createConfig($input)->validate(),
                    'schema_parser' => new EntityTypeSchemaXmlParser(),
                    'output_handler' => function ($message) use ($output) {
                        $output->writeln($message);
                    }
                )
            );
        }

        return $this->service;
    }

    protected function createConfig(InputInterface $input)
    {
        $config_path = $input->getOption('config');
        if (empty($config_path)) {
            $config_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'trellis.ini';
        }

        if (!is_readable($config_path)) {
            throw new NotReadableException(
                sprintf("Config file is not readable at location: `%s`", $config_path)
            );
        }

        $config_parser = new ConfigIniParser();
        $service_config = $config_parser->parse($config_path);

        return $service_config;
    }

    protected function getLookupDir(InputInterface $input)
    {
        $lookup_dir = $input->getOption('directory');
        if (empty($lookup_dir)) {
            $lookup_dir = getcwd();
        }

        return $lookup_dir;
    }

    protected function getEntityTypeSchemaPath(InputInterface $input)
    {
        $schema_path = $input->getOption('schema');
        if (empty($schema_path)) {
            $schema_path = $this->getLookupDir($input) . DIRECTORY_SEPARATOR . 'trellis.xml';
        }

        return $schema_path;
    }
}
