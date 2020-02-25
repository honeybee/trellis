<?php

namespace Trellis\CodeGen\Parser\Config;

use Trellis\CodeGen\Config;
use Trellis\CodeGen\Parser\ParserInterface;
use Trellis\Common\BaseObject;
use Trellis\Common\Error\InvalidConfigException;
use Trellis\Common\Error\NotReadableException;
use Trellis\Common\Error\NotWritableException;
use Trellis\Common\Error\ParseException;

class ConfigIniParser extends BaseObject implements ParserInterface
{
    public function parse($ini_file, array $options = [])
    {
        $config_dir = dirname($ini_file);
        $settings = $this->loadIniFile($ini_file);

        return new Config(
            array(
                'bootstrap_file' => $this->resolveBootstrapFile($settings, $config_dir),
                'deploy_method' => $this->resolveDeployMethod($settings),
                'deploy_dir' => $this->resolveDeployDirectory($settings, $config_dir),
                'cache_dir' => $this->resolveCacheDirectory($settings, $config_dir),
                'plugin_settings' => $this->createPluginData($settings, $config_dir),
                'entity_suffix' => $this->resolveEntitySuffix($settings),
                'type_suffix' => $this->resolveTypeSuffix($settings),
                'embed_entity_suffix' => $this->resolveEmbedEntitySuffix($settings),
                'embed_type_suffix' => $this->resolveEmbedTypeSuffix($settings),
                'referenced_entity_suffix' => $this->resolveReferencedEntitySuffix($settings),
                'referenced_type_suffix' => $this->resolveReferencedTypeSuffix($settings),
                'template_directory' => $this->resolveTemplateDirectory($settings, $config_dir),
            )
        );
    }

    protected function loadIniFile($ini_file)
    {
        if (!is_readable(realpath($ini_file))) {
            throw new NotReadableException(
                sprintf('Unable to read config file at path: `%s`', $ini_file)
            );
        }

        $settings = @parse_ini_file($ini_file, true);
        if ($settings === false) {
            throw new ParseException(
                sprintf('Unable to parse given config file: `%s`', $ini_file)
            );
        }

        return $settings;
    }

    protected function resolveDeployMethod(array $settings)
    {
        if (isset($settings['deploy_method'])) {
            $deploy_method = $settings['deploy_method'];
        } else {
            $deploy_method = 'copy';
        }

        return $deploy_method;
    }

    protected function resolveDeployDirectory(array $settings, $config_dir)
    {
        if (!isset($settings['deploy_dir'])) {
            throw new InvalidConfigException(
                "Missing 'deploy_dir' setting within the provided config (.ini)file."
            );
        }

        $deploy_directory = null;
        if ($settings['deploy_dir'][0] === '.') {
            $deploy_directory = $this->resolveRelativePath(
                $settings['deploy_dir'],
                $config_dir
            );
        } else {
            $deploy_directory = $this->fixPath($settings['deploy_dir']);
        }

        return $deploy_directory;
    }

    protected function resolveCacheDirectory(array $settings, $config_dir)
    {
        if (!isset($settings['cache_dir'])) {
            throw new InvalidConfigException(
                "Missing 'cache_dir' setting within the provided config (.ini)file."
            );
        }

        $cache_directory = null;
        if ($settings['cache_dir'][0] === '.') {
            $cache_directory = $this->resolveRelativePath(
                $settings['cache_dir'],
                $config_dir
            );
        } else {
            $cache_directory = $this->fixPath($settings['cache_dir']);
        }

        return $cache_directory;
    }

    protected function resolveTemplateDirectory(array $settings, $config_dir)
    {
        if (!isset($settings['template_directory'])) {
            return '';
        }
        $tpl_directory = null;
        if ($settings['template_directory'][0] === '.') {
            $tpl_directory = $this->resolveRelativePath(
                $settings['template_directory'],
                $config_dir
            );
        } else {
            $tpl_directory = $this->fixPath($settings['template_directory']);
        }

        if (!is_dir($tpl_directory)) {
            throw new InvalidConfigException(
                "Unable to resolve given template_directory to an existing file system path."
            );
        }

        return $tpl_directory;
    }

    protected function resolveBootstrapFile(array $settings, $config_dir)
    {
        $bootstrap_file = null;
        if (isset($settings['bootstrap_file'])) {
            if ($settings['bootstrap_file'][0] === '.') {
                $bootstrap_file = $this->resolveRelativePath(
                    $settings['bootstrap_file'],
                    $config_dir
                );
            } else {
                $bootstrap_file = $this->fixPath($settings['bootstrap_file']);
            }
            if (!is_readable($bootstrap_file)) {
                throw new NotReadableException(
                    sprintf('Unable to read bootstrap file at path: `%s`', $bootstrap_file)
                );
            }
        }

        return $bootstrap_file;
    }

    protected function createPluginData(array $settings, $config_dir)
    {
        $plugin_settings = [];
        foreach ($settings as $setting_label => $setting_data) {
            if (!preg_match('/^plugin\:/', $setting_label)) {
                continue;
            }

            $label_parts = explode(':', $setting_label);
            $plugin_class = array_pop($label_parts);
            $current_settings = $setting_data;
            foreach ($current_settings as $key => &$value) {
                if (preg_match('~^(\./|\.\./)~is', $value)) {
                    $value = $this->resolveRelativePath($value, $config_dir);
                }
            }
            $plugin_settings[$plugin_class] = $current_settings;
        }

        return $plugin_settings;
    }

    protected function resolveEntitySuffix(array $settings)
    {
        return isset($settings['entity_suffix']) ? $settings['entity_suffix'] : null;
    }

    protected function resolveTypeSuffix(array $settings)
    {
        return isset($settings['type_suffix']) ? $settings['type_suffix'] : null;
    }

    protected function resolveEmbedTypeSuffix(array $settings)
    {
        return isset($settings['embed_type_suffix']) ? $settings['embed_type_suffix'] : null;
    }

    protected function resolveEmbedEntitySuffix(array $settings)
    {
        return isset($settings['embed_entity_suffix']) ? $settings['embed_entity_suffix'] : null;
    }

    protected function resolveReferencedTypeSuffix(array $settings)
    {
        return isset($settings['referenced_type_suffix']) ? $settings['referenced_type_suffix'] : null;
    }

    protected function resolveReferencedEntitySuffix(array $settings)
    {
        return isset($settings['referenced_entity_suffix']) ? $settings['referenced_entity_suffix'] : null;
    }

    protected function resolveRelativePath($path, $base)
    {
        $fullpath_parts = [];
        $path_parts = explode(DIRECTORY_SEPARATOR, $this->fixPath($base) . $path);
        foreach ($path_parts as $path_part) {
            if ($path_part === '..') {
                array_pop($fullpath_parts);
            } elseif ($path_part !== '.') {
                $fullpath_parts[] = $path_part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $fullpath_parts);
    }

    protected function fixPath($path)
    {
        $fixed_path = $path;
        if (empty($path)) {
            return $fixed_path;
        }
        if (DIRECTORY_SEPARATOR != $path[strlen($path) - 1]) {
            $fixed_path .= DIRECTORY_SEPARATOR;
        }

        return $fixed_path;
    }
}
