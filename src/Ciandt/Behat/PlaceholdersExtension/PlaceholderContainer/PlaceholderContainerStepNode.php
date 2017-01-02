<?php


namespace Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer;

use Behat\Gherkin\Node\StepNode;

/**
 * Description of DecoratedStepNode
 *
 * @author bwowk
 */
class PlaceholderContainerStepNode extends StepNode implements PlaceholderContainer{
    
    private $stepNode;
    
    private $variant;
    
    private $configKey;
    
    private $sectionKey;
    
    function __construct($stepNode,$configKey, $sectionKey,  $variant) {
        $this->stepNode = $stepNode;
        $this->configKey = $configKey;
        $this->sectionKey = $sectionKey;
        $this->variant = $variant;
    }

    
    public function getArguments() {
        return $this->stepNode->getArguments();
    }

    public function getKeyword() {
        return $this->stepNode->getKeyword();
    }

    public function getKeywordType() {
        return $this->stepNode->getKeywordType();
    }

    public function getLine() {
        return $this->stepNode->getLine();
    }

    public function getNodeType() {
        return $this->stepNode->getNodeType();
    }

    public function getText() {
        return $this->stepNode->getText();
    }

    public function getType() {
        return $this->stepNode->getType();
    }

    public function hasArguments() {
        return $this->stepNode->hasArguments();
    }

    public function getConfigKey() {
        return $this->configKey;
    }

    public function getSectionKey() {
        return $this->sectionKey;
    }

    public function getVariant() {
        return $this->variant;
    }


}
