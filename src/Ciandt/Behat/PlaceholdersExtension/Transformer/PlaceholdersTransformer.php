<?php

namespace Ciandt\Behat\PlaceholdersExtension\Transformer;

use Behat\Behat\Definition\Call\DefinitionCall;
use Behat\Behat\Transformation\Transformer\ArgumentTransformer;
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;
use Ciandt\Behat\PlaceholdersExtension\Subscriber\BeforeScenarioSubscriber;
use Ciandt\Behat\PlaceholdersExtension\Transformation\RuntimePlaceholdersTransformation;

/**
 * Transforms a single argument value.

 */
class PlaceholdersTransformer implements ArgumentTransformer
{
    private $beforeScenarioSubscriber;
    
    private $placeholdersRepository;
    
    public function __construct(PlaceholdersRepository $placeholdersRepository, BeforeScenarioSubscriber $beforeScenarioSubscriber) {
        $this->placeholdersRepository = $placeholdersRepository;
        $this->beforeScenarioSubscriber = $beforeScenarioSubscriber;
    }

    
    public function supportsDefinitionAndArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentValue) {
        $tags = $this->beforeScenarioSubscriber->getScenarioTags();
        return RuntimePlaceholdersTransformation::supportsDefinitionAndArgument($tags, $argumentValue);        
    }

    public function transformArgument(DefinitionCall $definitionCall, $argumentIndex, $argumentValue) {
        $tags = $this->beforeScenarioSubscriber->getScenarioTags();
        $transformedArgument = $argumentValue;
        if (RuntimePlaceholdersTransformation::supportsDefinitionAndArgument($tags, $argumentValue)){
            $transformedArgument = RuntimePlaceholdersTransformation::transformArgument(
                    $transformedArgument,
                    $this->placeholdersRepository,
                    $tags);
        }
        
        return $transformedArgument;
    }

}
