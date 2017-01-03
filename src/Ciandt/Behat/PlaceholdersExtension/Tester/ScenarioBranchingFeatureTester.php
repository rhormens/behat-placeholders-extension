<?php
namespace Ciandt\Behat\PlaceholdersExtension\Tester;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\SuccessfulSetup;
use Behat\Testwork\Tester\Setup\SuccessfulTeardown;
use Behat\Testwork\Tester\SpecificationTester;
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
    
    private function splitScenarioPerVariants(ScenarioInterface $scenarioLike, FeatureNode $feature){
        $scenarioTags = $scenarioLike->getTags();
        $featureTags = $feature->getTags();
        $tags = array_merge($scenarioTags,$featureTags);
        
        $variants = PlaceholderUtils::filterVariantTags($tags,false);
        
            if (count($variants) <= 1) {
                return array($scenarioLike);
            } else {
                return $this->forkScenario($scenarioLike, $variants);
            }
        
    }
    
    private function forkScenario(ScenarioInterface $scenarioLike, $variants)
    {
        $scenarios = array();
        $nonVariantTags = PlaceholderUtils::filterVariantTags($scenarioLike->getTags(),true);
        foreach ($variants as $variant) {
            $tags = array_merge($nonVariantTags, array($variant));
            if ($scenarioLike instanceof ScenarioNode){
            $scenarios[] = new ScenarioNode(
                $scenarioLike->getTitle(),
                $tags,
                $scenarioLike->getSteps(),
                $scenarioLike->getKeyword(),
                $scenarioLike->getLine()
            );
            } elseif ($scenarioLike instanceof OutlineNode){
            $scenarios[] = new OutlineNode(
                $scenarioLike->getTitle(),
                $tags,
                $scenarioLike->getSteps(),
                $scenarioLike->getExampleTable(),
                $scenarioLike->getKeyword(),
                $scenarioLike->getLine()
            );    
            }
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
