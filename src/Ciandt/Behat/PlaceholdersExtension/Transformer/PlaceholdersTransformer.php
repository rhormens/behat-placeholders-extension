<?php

namespace Ciandt\Behat\PlaceholdersExtension\Transformer;

use Behat\Behat\Transformation\Transformer\ArgumentTransformer;
use Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer\PlaceholderContainerStepNode;
use Ciandt\Behat\PlaceholdersExtension\Transformation\RuntimePlaceholdersTransformation;
use Behat\Behat\Definition\Call\DefinitionCall;
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;

/**
 * Transforms a single argument value.

 */
class PlaceholdersTransformer implements ArgumentTransformer
{
    
    private $placeholdersRepository;
    
    public function __construct(PlaceholdersRepository $placeholdersRepository) {
        $this->placeholdersRepository = $placeholdersRepository;
    }

    
    public function supportsDefinitionAndArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentValue) {
        return RuntimePlaceholdersTransformation::supportsDefinitionAndArgument($definitionCall, $argumentValue);        
    }

    public function transformArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentValue) {
        $transformedArgument = $argumentValue;
        if (RuntimePlaceholdersTransformation::supportsDefinitionAndArgument($definitionCall, $argumentValue)){
            $transformedArgument = RuntimePlaceholdersTransformation::transformArgument(
                    $transformedArgument,
                    $this->placeholdersRepository,
                    $definitionCall->getStep());
        }
        
        return $transformedArgument;
    }

}
