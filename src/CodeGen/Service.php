<?php

namespace Trellis\CodeGen;

use Trellis\Common\BaseObject;
use Trellis\CodeGen\Schema\EntityTypeSchema;
use Trellis\CodeGen\ClassBuilder\Factory;
use Trellis\CodeGen\ClassBuilder\ClassContainerList;
use Trellis\CodeGen\ClassBuilder\BuildCache;

class Service extends BaseObject
{
    protected $config;

    protected $schema_parser;

    protected $class_builder_factory;

    protected $build_cache;

    protected $output_handler;

    public function __construct(array $state = [])
    {
        parent::__construct($state);

        $this->class_builder_factory = new Factory($this->config);
        $this->output_handler = function ($message) {
            echo $message . PHP_EOL;
        };
    }

    public function generate($type_schema_path)
    {
        $type_schema = $this->schema_parser->parse($type_schema_path);

        $class_container_list = new ClassContainerList();
        $class_container_list->addItems(
            array_map(
                function ($builder) {
                    return $builder->build();
                },
                $this->createClassBuilders($type_schema)
            )
        );

        $this->build_cache->generate($class_container_list);
        $this->executePlugins($type_schema);
    }

    public function deploy($type_schema_path)
    {
        $type_schema = $this->schema_parser->parse($type_schema_path);

        $class_container_list = new ClassContainerList();
        $class_container_list->addItems(
            array_map(
                function ($builder) {
                    return $builder->build();
                },
                $this->createClassBuilders($type_schema)
            )
        );

        $this->build_cache->deploy($class_container_list, $this->config->getDeployMethod());
    }

    protected function setConfig(Config $config)
    {
        $this->config = $config;

        $this->build_cache = new BuildCache(
            array(
                'cache_directory' => $this->config->getCacheDir(),
                'deploy_directory' => $this->config->getDeployDir()
            )
        );

        $bootstrap = function ($bootstrap_file = null) {
            if ($bootstrap_file) {
                require_once $bootstrap_file;
            }
        };

        $bootstrap($this->config->getBootstrapFile());
    }

    protected function createClassBuilders(EntityTypeSchema $type_schema)
    {
        $this->class_builder_factory->setEntityTypeSchema($type_schema);

        $entity_type = $type_schema->getEntityTypeDefinition();
        $class_builders = $this->class_builder_factory->createClassBuildersForType($entity_type);

        foreach ($type_schema->getUsedEmbedDefinitions($entity_type) as $embed_type) {
            $embed_type_builders = $this->class_builder_factory->createClassBuildersForType($embed_type);
            $class_builders = array_merge($class_builders, $embed_type_builders);
        }

        foreach ($type_schema->getUsedReferenceDefinitions($entity_type) as $reference) {
            $reference_builders = $this->class_builder_factory->createClassBuildersForType($reference);
            $class_builders = array_merge($class_builders, $reference_builders);
        }

        return $class_builders;
    }

    protected function executePlugins(EntityTypeSchema $type_schema)
    {
        foreach ($this->config->getPluginSettings() as $plugin_class => $plugin_options) {
            if (class_exists($plugin_class)) {
                if (is_a($plugin_class, '\\Trellis\\CodeGen\\PluginInterface', true)) {
                    $plugin = new $plugin_class($plugin_options);
                    $plugin->execute($type_schema);
                } else {
                    $this->writeMessage(
                        sprintf(
                            '<warning>Plugin class: `%s`, does not implement the PluginInterface interface.</warning>',
                            $plugin_class
                        )
                    );
                }
            } else {
                $this->writeMessage(
                    sprintf(
                        '<warning>Unable to load plugin class: `%s`</warning>',
                        $plugin_class
                    )
                );
            }
        }
    }

    protected function writeMessage($message)
    {
        $write_message = $this->output_handler;
        $write_message($message);
    }
}
