<?php


namespace Ciandt\Behat\PlaceholdersExtension\Tester;

use Behat\Behat\Tester\ScenarioTester;
use Behat\Gherkin\Node\BackgroundNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\SuccessfulSetup;
use Behat\Testwork\Tester\Setup\SuccessfulTeardown;
use Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer\PlaceholderContainerStepNode;
use Ciandt\Behat\PlaceholdersExtension\Utils\PlaceholderUtils;

/**
 * Description of StepDecoratingScenarioTester
 *
 * @author bwowk
 */
class StepDecoratingScenarioTester implements ScenarioTester{
    
    private $baseTester;
    private $configKey;
    private $sectionKey;
    private $variant;
    
    public function __construct(ScenarioTester $baseTester) {
        $this->baseTester = $baseTester;
    }

    public function setUp(Environment $env, FeatureNode $feature, ScenarioInterface $scenario, $skip) {
        return new SuccessfulSetup();
    }

    public function tearDown(Environment $env, FeatureNode $feature, ScenarioInterface $scenario, $skip, TestResult $result) {
        return new SuccessfulTeardown();
    }

    public function test(Environment $env, FeatureNode $feature, ScenarioInterface $scenario, $skip) {
        $scenarioTags = $scenario->getTags();
        $featureTags = $feature->getTags();
        $tags = array_merge($scenarioTags,$featureTags);
        // If there's no config tag, proceed with undecorated steps
        if (!PlaceholderUtils::getConfigTag($tags)){
            return $this->baseTester->test($env, $feature, $scenario, $skip);
        }
        // detect config and variant tags
        $this->scanMeaningfulTags($tags);
        
        $decoratedFeature = $this->decorateBackgroundSteps($feature);
        $decoratedScenario = $this->decorateScenarioSteps($scenario);
        return $this->baseTester->test($env, $decoratedFeature, $decoratedScenario, $skip);
    }
    
    private function decorateScenarioSteps(ScenarioInterface $undecoratedScenario){
        $decoratedSteps = array();
        foreach ($undecoratedScenario->getSteps() as $step){
            $decoratedSteps[] = new PlaceholderContainerStepNode(
                    $step,
                    $this->configKey,
                    $this->sectionKey,
                    $this->variant);
        }
        return new ScenarioNode(
                $undecoratedScenario->getTitle(),
                $undecoratedScenario->getTags(),
                $decoratedSteps,
                $undecoratedScenario->getKeyword(),
                $undecoratedScenario->getLine());
    }
    
    private function scanMeaningfulTags($tags){
        $configTag = PlaceholderUtils::getConfigTag($tags);
        $this->configKey = PlaceholderUtils::getConfigKey($configTag);
        $this->sectionKey = PlaceholderUtils::getSectionKey($configTag);
        // At this point, variant tags should have been splitted between sepparate scenarios
        $this->variant = PlaceholderUtils::getVariant($tags);
    }
    
    private function decorateBackgroundSteps(FeatureNode $feature){
        if (!$feature->hasBackground()) {
            return $feature;
        }
        $undecoratedBackground = $feature->getBackground();
        $decoratedSteps = array();
        foreach ($undecoratedBackground->getSteps() as $step){
            $decoratedSteps[] = new PlaceholderContainerStepNode(
                    $step,
                    $this->configKey,
                    $this->configSection,
                    $this->variant);
        }
        return new BackgroundNode(
                $undecoratedBackground->getTitle(),
                $decoratedSteps,
                $undecoratedBackground->getKeyword(),
                $undecoratedBackground->getLine());
    }

}
