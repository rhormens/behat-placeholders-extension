<?php
namespace Ciandt\Behat\PlaceholdersExtension\Config;

use Symfony\Component\Yaml\Yaml;
use Ciandt\Behat\PlaceholdersExtension\Utils\PlaceholderUtils;
use Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer\PlaceholderContainer;
use Ciandt\Behat\PlaceholdersExtension\Exception\MissingSectionException;
use Ciandt\Behat\PlaceholdersExtension\Exception\UndefinedPlaceholderException;

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
        $this->configs = $this->loadConfigFiles($configs_mapping);
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
        return $placeholder_maps;
    }

    public function getConfig($key)
    {
        if (key_exists($key, $this->configs)) {
            return $this->configs[$key]['config'];
        }
        return null;
    }

    public function getFilePath($key)
    {
         if (key_exists($key, $this->configs)) {
            return $this->configs[$key]['path'];
        }
        return null;
    }
       
    public function getEnvironment()
    {
        return $this->environment;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }
    
    public function getReplacement($placeholder, $tags) {
        $configTag = PlaceholderUtils::getConfigTag($tags);
        $configKey = PlaceholderUtils::getConfigKey($configTag);
        $section = PlaceholderUtils::getSectionKey($configTag);
        $placeholders = $this->getSectionPlaceholders($configKey, $section);
        
        $variantTags = PlaceholderUtils::filterVariantTags($tags, false);
        $variant = end($variantTags);
        $environment = $this->getEnvironment();
        $configPath = $this->getFilePath($configKey);
        $keys = array('$' . $variant, '$' . $environment, $placeholder);
        $treePosition = "$configPath>$section>placeholders";

        return $this->recursivePlaceholderSearch($keys, $placeholders, $treePosition);
        
    }
    
    private function recursivePlaceholderSearch($keys, $values, $treePosition)
    {
        if (empty($keys) || is_string($values)) {
            return $values;
        }
        $key = array_pop($keys);
        if (key_exists($key, $values)) {
            return $this->recursivePlaceholderSearch($keys, $values[$key], "$treePosition>$key");
        } elseif (key_exists('$default', $values)) {
            return $this->recursivePlaceholderSearch($keys, $values['$default'], $treePosition . '>$default');
        } else {
            throw new UndefinedPlaceholderException("No placeholder is defined on $treePosition>$key");
        }
    }
    
    private function getSectionPlaceholders($configKey, $section){
        $config = $this->getConfig($configKey);
        if (!key_exists($section, $config)){
            throw new MissingSectionException(
                    $configKey,
                    $this->getFilePath($configKey),
                    $section);
        }
        return $config[$section]['placeholders'];
    }
            
}
