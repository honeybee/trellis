<?php

namespace Trellis\Tests\CodeGen\Parser\Config;

use Trellis\Tests\TestCase;
use Trellis\CodeGen\Parser\Config\ConfigIniParser;

class ConfigIniParserTest extends TestCase
{
    const FIXTURE_NON_PARSEABLE_CONFIG = 'non_parseable.ini';

    const FIXTURE_CONFIG_WITH_RELATIVE_PATHS = 'relative_paths.ini';

    const FIXTURE_VALID_CONFIG = 'valid_config.ini';

    protected $fixtures_dir;

    public function setUp()
    {
        $this->fixtures_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR;
    }

    public function testCreateConfigReader()
    {
        $config = new ConfigIniParser();

        $this->assertInstanceOf('Trellis\CodeGen\Parser\Config\ConfigIniParser', $config);
    }

    public function testValidConfig()
    {
        $parser = new ConfigIniParser();
        $config = $parser->parse($this->fixtures_dir . self::FIXTURE_VALID_CONFIG);
        $expected_array = [
            'bootstrap_file' => __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'bootstrap.php',
            'cache_dir' => '/tmp/trellis_cache_test_dir/',
            'deploy_dir' => '/tmp/trellis_deploy_test_dir/',
            'deploy_method' => 'copy',
            'plugin_settings' => [
                'MappingGeneratorPlugin' => [
                    'deploy_path' => '/tmp/erpen-derp/mapping.json'
                ]
            ],
            '@type' => 'Trellis\CodeGen\Config',
            'options' => [],
            'entity_suffix' => null,
            'type_suffix' => null,
            'embed_entity_suffix' => null,
            'embed_type_suffix' => null,
            'referenced_entity_suffix' => null,
            'referenced_type_suffix' => null,
            'template_directory' => realpath(__DIR__ . '/../../../../src/CodeGen/ClassBuilder/templates')
        ];

        $this->assertEquals($expected_array, $config->toArray());
    }

    public function testReadWithRelativePaths()
    {
        $parser = new ConfigIniParser();
        $config = $parser->parse($this->fixtures_dir . self::FIXTURE_CONFIG_WITH_RELATIVE_PATHS);

        $expected_base_path = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $expected_cache_dir = $expected_base_path . DIRECTORY_SEPARATOR . 'trellis_cache_dir';
        $expected_deploy_dir = $expected_base_path . DIRECTORY_SEPARATOR . 'trellis_deploy_dir';

        $expected_array = [
            'bootstrap_file' => null,
            'cache_dir' => $expected_cache_dir,
            'deploy_dir' => $expected_deploy_dir,
            'deploy_method' => 'copy',
            'plugin_settings' => [],
            '@type' => 'Trellis\CodeGen\Config',
            'options' => [],
            'entity_suffix' => null,
            'type_suffix' => null,
            'embed_entity_suffix' => null,
            'embed_type_suffix' => null,
            'referenced_entity_suffix' => null,
            'referenced_type_suffix' => null,
            'template_directory' => null
        ];

        $this->assertEquals($expected_array, $config->toArray());
    }

    /**
     * @expectedException Trellis\Common\Error\FileSystemException
     */
    public function testNonReadableConfig()
    {
        $parser = new ConfigIniParser();

        $parser->parse($this->fixtures_dir . 'this_config_does_not_exist.ini');
        // @codeCoverageIgnoreStart
    }   // @codeCoverageIgnoreEnd

    /**
     * @expectedException Trellis\Common\Error\ParseException
     */
    public function testNonParseableConfig()
    {
        $parser = new ConfigIniParser();

        $parser->parse($this->fixtures_dir . self::FIXTURE_NON_PARSEABLE_CONFIG);
        // @codeCoverageIgnoreStart
    }   // @codeCoverageIgnoreEnd
}
