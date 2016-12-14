<?php


namespace Ciandt\Behat\PlaceholdersExtension\Tester;

use Behat\Behat\Tester\OutlineTester;
use Behat\Behat\Tester\ScenarioTester;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Environment\EnvironmentManager;
use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Result\TestResults;
use Behat\Testwork\Tester\Result\TestWithSetupResult;
use Behat\Testwork\Tester\Setup\SuccessfulSetup;
use Behat\Testwork\Tester\Setup\SuccessfulTeardown;
use Behat\Testwork\Tester\SpecificationTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use Ciandt\Behat\PlaceholdersExtension\Config\ConfigsRepository;

/**
 * Tester executing feature tests in the runtime.
 *
 */
final class ScenarioBranchingFeatureTester implements SpecificationTester {

  /**
   * @var SpecificationTester
   */
  private $baseTester;

  /**
   * @var array
   */
  private $variantTags;

  /**
   * @var array
   */
  private $configsRepo;

  /**
   * Initializes tester.
   *
   * @param SpecificationTester $baseTester
   */
  public function __construct(SpecificationTester $baseTester, $variantTags, ConfigsRepository $configsRepo ) {
    $this->baseTester = $baseTester;
    $this->variantTags = $variantTags;
    $this->configsRepo = $configsRepo;
  }

  /**
   * {@inheritdoc}
   */
  public function setUp(Environment $env, $spec, $skip) {
    return new SuccessfulSetup();
  }

  /**
   * {@inheritdoc}
   */
  public function test(Environment $env, $feature, $skip = false) {
    $results = array();
    $tester = $this->baseTester;
    $variantTags = $this->variantTags;
    if ($variantTags) {
      return $tester->test($env, $this->reconstructFeature($feature), $skip);
    }
    else {
      return $tester->test($env, $feature, $skip);
    }
  }

  private function reconstructFeature(FeatureNode $feature) {
    $scenarios = array();
    foreach ($feature->getScenarios() as $scenario) {
      $configTag = $this->getScenarioConfigTag($scenario);
      $scenarioVariants = array_intersect($scenario->getTags(), $this->variantTags);
      if (count($scenarioVariants) == 1){
        $injectedScenario = new ScenarioNode(
          $scenario->getTitle(),
          $scenario->getTags(),
          $this->injectParametersOnSteps($scenario->getSteps(), end($scenarioVariants),$configTag),
          $scenario->getKeyword(),
          $scenario->getLine());
        $scenarios[] = $injectedScenario;
      }
      elseif (count($scenarioVariants) > 1) {
        $scenarios = array_merge($scenarios, $this->forkScenario($scenario, $scenarioVariants, $configTag));
      }
      else {
        $scenarios[] = $scenario;
      }
    }

    return new FeatureNode(
        $feature->getTitle(), $feature->getDescription(), $feature->getTags(), $feature->getBackground(), $scenarios, $feature->getKeyword(), $feature->getLanguage(), $feature->getFile(), $feature->getLine()
    );
  }
  
  private function getScenarioConfigTag(ScenarioNode $scenario){
    $availableTags = $this->configsRepo->getTags();
    $configTags = array_intersect($scenario->getTags(),$availableTags );
    
    if (count($configTags) > 1) {
      throw new \Exception ("Scenario {$scenario->getTitle()}"
      . " should have only ONE of the following Placeholder"
        . " File Tags: " . implode(',', $availableTags));
    }
    
    if (count($configTags) == 1) {
      return end($configTags);
    }
    
    return FALSE;
  
  }


  private function forkScenario(ScenarioNode $scenario, $variants, $configTag) {
    $scenarios = array();
    $nonVariantTags = array_filter($scenario->getTags(), function ($val) {
      if (!in_array($val, $this->variantTags))
        return TRUE;
    }
    );
    foreach ($variants as $variant) {
      $tags = array_merge($nonVariantTags, array($variant));
      $steps = $this->injectParametersOnSteps($scenario->getSteps(), $variant, $configTag);
      $variantScenario = new ScenarioNode(
          $scenario->getTitle(),
          $tags,
          $steps,
          $scenario->getKeyword(),
          $scenario->getLine());
      $scenarios[] = $variantScenario;
    }
    return $scenarios;
  }

  private function injectParametersOnSteps($steps, $variant, $configTag) {
    $injectedSteps = array();
    foreach ($steps as $step) {
      $newStep = clone $step;
      $newStep->variant = $variant;
      if ($configTag) $newStep->configTag = $configTag;
      $injectedSteps[] = $newStep;
    }
    return $injectedSteps;
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(Environment $env, $spec, $skip, TestResult $result) {
    return new SuccessfulTeardown();
  }

}
