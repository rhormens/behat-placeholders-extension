<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    
   
    public static function supportsDefinitionAndArgument(DefinitionCall $definitionCall, $argumentValue);
    
    public static function transformArgument($argumentValue, PlaceholdersRepository $repository,
            PlaceholderContainer $container);
    
}
