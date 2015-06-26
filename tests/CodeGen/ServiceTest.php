<?php

namespace Trellis\Tests\CodeGen;

use Trellis\Tests;
use Trellis\CodeGen\Service;
use Trellis\CodeGen\Config;
use Trellis\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;
use Symfony\Component\Filesystem;

class ServiceTest extends Tests\TestCase
{
    protected $config;

    protected $schema_path;

    public function testBuildSchema()
    {
        $codegen_service = new Service(
            array(
                'config' => $this->config,
                'schema_parser' => new EntityTypeSchemaXmlParser()
            )
        );

        $codegen_service->generate($this->schema_path);
        // @todo assert validity of the generated code inside the configured cache directory.
    }

    public function testDeployMethodMove()
    {
        $this->config->setDeployMethod('move');

        $codegen_service = new Service(
            array(
                'config' => $this->config,
                'schema_parser' => new EntityTypeSchemaXmlParser()
            )
        );

        $codegen_service->generate($this->schema_path);
        $codegen_service->deploy($this->schema_path);
        // @todo assert validity of the generated code inside the configured deploy directory.
    }

    public function testDeployMethodCopy()
    {
        $this->config->setDeployMethod('copy');

        $codegen_service = new Service(
            array(
                'config' => $this->config,
                'schema_parser' => new EntityTypeSchemaXmlParser()
            )
        );

        $codegen_service->generate($this->schema_path);
        $codegen_service->deploy($this->schema_path);
        // @todo assert validity of the generated code inside the configured deploy directory.
    }

    protected function setUp()
    {
        $tmp_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $tmp_cache_path = $tmp_dir . 'testing_cache_' . mt_rand() . DIRECTORY_SEPARATOR;
        $tmp_deploy_path = $tmp_dir . 'testing_deploy_' . mt_rand() . DIRECTORY_SEPARATOR;

        $this->config = new Config(
            array(
                'cache_dir' => $tmp_cache_path,
                'deploy_dir' => $tmp_deploy_path,
                'plugin_settings' => []
            )
        );

        $this->schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem\Filesystem();
        $filesystem->remove($this->config->getCacheDir());
        $filesystem->remove($this->config->getDeployDir());
    }
}
