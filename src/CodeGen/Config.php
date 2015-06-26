<?php

namespace Trellis\CodeGen;

use Trellis\Common\Options;
use Trellis\Common\Configurable;
use Trellis\Common\Error\InvalidConfigException;

class Config extends Configurable
{
    protected $entity_suffix;
    protected $type_suffix;
    protected $embed_entity_suffix;
    protected $embed_type_suffix;
    protected $referenced_entity_suffix;
    protected $referenced_type_suffix;
    protected $bootstrap_file;
    protected $cache_dir;
    protected $deploy_dir;
    protected $deploy_method;
    protected $plugin_settings;
    protected $template_directory;

    /**
     * Override the Object's constructor to always initialize some properties.
     *
     * @param array $state data to set on the object (key-value pairs)
     */
    public function __construct(array $state = [])
    {
        parent::__construct($state);

        if (empty($this->deploy_method)) {
            $this->deploy_method = 'copy';
        }

        if (empty($this->plugin_settings)) {
            $this->plugin_settings = new Options();
        }
    }

    public function getBootstrapFile()
    {
        return $this->bootstrap_file;
    }

    public function getCacheDir()
    {
        return $this->cache_dir;
    }

    public function setCacheDir($cache_dir)
    {
        $this->cache_dir = $cache_dir;
    }

    public function getDeployDir()
    {
        return $this->deploy_dir;
    }

    public function setDeployDir($deploy_dir)
    {
        $this->deploy_dir = $deploy_dir;
    }

    public function getDeployMethod()
    {
        return $this->deploy_method;
    }

    public function setDeployMethod($deploy_method)
    {
        $this->deploy_method = $deploy_method;
    }

    public function getPluginSettings()
    {
        return $this->plugin_settings;
    }

    public function setPluginSettings($settings)
    {
        $this->plugin_settings = new Options($settings);
    }

    public function getEntitySuffix($default = "")
    {
        return $this->entity_suffix ?: $default;
    }

    public function getTypeSuffix($default = "")
    {
        return $this->type_suffix ?: $default;
    }

    public function getEmbedEntitySuffix($default = "")
    {
        return $this->embed_entity_suffix ?: $default;
    }

    public function getEmbedTypeSuffix($default = "")
    {
        return $this->embed_type_suffix ?: $default;
    }

    public function getReferencedEntitySuffix($default = "")
    {
        return $this->referenced_entity_suffix ?: $default;
    }

    public function getReferencedTypeSuffix($default = "")
    {
        return $this->referenced_type_suffix ?: $default;
    }

    public function getTemplateDirectory($default = "")
    {
        return $this->template_directory ?: $default;
    }

    public function validate()
    {
        $cache_directory = $this->getCacheDir();
        if (empty($cache_directory)) {
            throw new InvalidConfigException("Missing 'cache_dir' setting.");
        }

        $deploy_directory = $this->getDeployDir();
        if (empty($deploy_directory)) {
            throw new InvalidConfigException("Missing 'deploy_dir' setting.");
        }

        $deploy_method = $this->getDeployMethod();
        $valid_methods = array('copy', 'move');
        if (!in_array($deploy_method, $valid_methods)) {
            throw new InvalidConfigException(
                sprintf("Invalid deploy method '%s' passed to config.", $deploy_method)
            );
        }

        if (!empty($this->template_directory) && !is_dir($this->template_directory)) {
            throw new InvalidConfigException(
                sprintf("Invalid template directory setting given: %s.", $this->template_directory)
            );
        }

        return $this;
    }
}
