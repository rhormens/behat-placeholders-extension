<?php

namespace Ciandt\Behat\PlaceholdersExtension\Transformation;

use Behat\Behat\Definition\Call\DefinitionCall;
use Behat\Behat\Transformation\Transformation;
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;
use Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer\PlaceholderContainer;
/**
 *
 * @author bwowk
 */
interface PlaceholdersTransformation extends Transformation {
    
   
    public static function supportsDefinitionAndArgument($tags, $argumentValue);
    
    public static function transformArgument($argumentValue, PlaceholdersRepository $repository, $tags);
    
}
