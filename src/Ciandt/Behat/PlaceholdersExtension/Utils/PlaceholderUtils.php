<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ciandt\Behat\PlaceholdersExtension\Utils;

/**
 * Filters an array of tags, excluding or including only the variant tags
 *
 * @author bwowk
 */
class PlaceholderUtils
{
    private static $variantTags;

    public static function setVariantTags($variantTags)
    {
        self::$variantTags = $variantTags;
    }
    
    public static  function getConfigSection($tag){
        $tagParts = explode(':', tag);
        if (count($tagParts) == 2){
            return $tagParts[1];
        } else {
            return 'default';
        }
        
    }
    
    public static  function getConfigTag($tag){
        return explode(':', $tag)[0];
    }

    
    public static function filterVariantTags($tags, $exclude)
    {
        return array_filter($tags, function ($tag) use ($exclude) {
            return (in_array(self::getConfigTag($tag), self::$variantTags) xor $exclude);
        });
    }
}
