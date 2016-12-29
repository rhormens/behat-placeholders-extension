<?php
namespace Ciandt\Behat\PlaceholdersExtension\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Description of ConfigsRepository
 *
 * @author bwowk
 */
class PlaceholdersRepository
{

    private $configs;

    /**
     * @var string
     */
    private $environment;

    public function __construct($configs_mapping)
    {
        $this->configs = array();
        $this->loadConfigFiles($configs_mapping);
    }

    /**
     *
     * @return string[]
     * @todo read configs and also bring alternative @config:section tags
     */
    public function getTags()
    {
        return array_keys($this->configs);
    }

    /**
     *
     * @param type $config_files
     * @todo user %paths.base% value
     */
    private function loadConfigFiles($configs_mapping)
    {
        $placeholder_maps = array();
        foreach ($configs_mapping as $tag => $file_path) {
            $placeholder_maps[$tag]['config'] = Yaml::parse(file_get_contents($file_path));
            $placeholder_maps[$tag]['path'] = $file_path;
        }
        $this->configs = $placeholder_maps;
    }

    public function getConfigSection($tag, $section)
    {
        if ($this->hasTag($tag)) {
            return $this->configs[$tag]['config'][$section];
        }
        return null;
    }

    public function getFilePath($tag)
    {
        if ($this->hasTag($tag)) {
            return $this->configs[$tag]['path'];
        }
        return null;
    }

    public function hasTag($tag)
    {
        if (key_exists($tag, $this->configs)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getEnvironment()
    {
        return $this->environment;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
}
