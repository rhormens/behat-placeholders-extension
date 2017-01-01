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
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;
use Ciandt\Behat\PlaceholdersExtension\Utils\PlaceholderUtils;

/**
 * Tester executing feature tests in the runtime.
 *
 */
final class ScenarioBranchingFeatureTester implements SpecificationTester
{

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
    public function __construct(SpecificationTester $baseTester, PlaceholdersRepository $configsRepo)
    {
        $this->baseTester = $baseTester;
        $this->configsRepo = $configsRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(Environment $env, $spec, $skip)
    {
        return new SuccessfulSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function test(Environment $env, $feature, $skip = false)
    {
        $tester = $this->baseTester;
        $reconstructedFeature = $this->preprocessFeature($feature);
        return $tester->test($env, $reconstructedFeature, $skip);
    }

    private function preprocessFeature(FeatureNode $feature)
    {
        $scenarios = array();
        
        foreach ($feature->getScenarios() as $scenario) {
            $scenarios = array_merge($scenarios,$this->splitScenarioPerVariants($scenario,$feature));
        }

        return new FeatureNode(
            $feature->getTitle(),
            $feature->getDescription(),
            $feature->getTags(),
            $feature->getBackground(),
            $scenarios,
            $feature->getKeyword(),
            $feature->getLanguage(),
            $feature->getFile(),
            $feature->getLine()
        );
    }
    
    private function splitScenarioPerVariants(ScenarioNode $scenario, FeatureNode $feature){
        $scenarioTags = $scenario->getTags();
        $featureTags = $feature->getTags();
        $tags = array_merge($scenarioTags,$featureTags);
        
        $variants = PlaceholderUtils::filterVariantTags($tags,false);
        
            if (count($variants) <= 1) {
                return array($scenario);
            } else {
                return $this->forkScenario($scenario, $variants);
            }
        
    }
    
    private function forkScenario(ScenarioNode $scenario, $variants)
    {
        $scenarios = array();
        $nonVariantTags = PlaceholderUtils::filterVariantTags($scenario->getTags(),true);
        foreach ($variants as $variant) {
            $tags = array_merge($nonVariantTags, array($variant));
            $scenarios[] = new ScenarioNode(
                $scenario->getTitle(),
                $tags,
                $scenario->getSteps(),
                $scenario->getKeyword(),
                $scenario->getLine()
            );
        }
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(Environment $env, $spec, $skip, TestResult $result)
    {
        return new SuccessfulTeardown();
    }
}
