<?php
namespace Ciandt\Behat\PlaceholdersExtension\ServiceContainer;

use Ciandt\Behat\PlaceholdersExtension\Config\ConfigsRepository;
use Ciandt\Behat\PlaceholdersExtension\Tester\PerVariantScenarioTester;
use Ciandt\Behat\PlaceholdersExtension\Tester\PlaceholdersReplacer;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;

final class PlaceholdersExtension implements Extension
{

    const SCENARIO_TESTER_ID = 'tester.scenario';
    const SPECIFICATION_TESTER_ID = 'tester.specification';
    const OUTLINE_TESTER_ID = 'tester.outline';
    const PLACEHOLDER_REPLACER_ID = 'placeholders.replacer';

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'placeholders';
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('variant_tags')
            ->treatNullLike(array())
            ->info('Variant tags to iterate through')
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('config_files')
            ->useAttributeAsKey('tag')
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadScenarioForkingFeatureTester($container, $config['variant_tags']);
        $this->loadConfigsRepository($container, $config['config_files']);
        $this->loadStepTester($container, $config['variant_tags'], 'default');
        $this->loadPlaceholdersController($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadPlaceholdersController(ContainerBuilder $container)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Cli\PlaceholdersController', array(
            new Reference(self::PLACEHOLDER_REPLACER_ID)));
        $definition->addTag(CliExtension::CONTROLLER_TAG, array('priority' => 1));
        $container->setDefinition(CliExtension::CONTROLLER_TAG . '.placeholders', $definition);
    }

    /**
     * Loads event-dispatching feature tester.
     *
     * @param ContainerBuilder $container
     */
    protected function loadConfigsRepository(ContainerBuilder $container, $configs_mapping)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Config\ConfigsRepository', array(
            $configs_mapping
        ));
        $container->setDefinition(ConfigsRepository::CONFIGS_REPOSITORY_ID, $definition);
    }

    /**
     * Loads event-dispatching feature tester.
     *
     * @param ContainerBuilder $container
     */
    protected function loadScenarioForkingFeatureTester(ContainerBuilder $container, $variantTags)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Tester\ScenarioBranchingFeatureTester', array(
            new Reference(TesterExtension::SPECIFICATION_TESTER_ID),
            $variantTags,
            new Reference(ConfigsRepository::CONFIGS_REPOSITORY_ID)
        ));
        $definition->addTag(TesterExtension::SPECIFICATION_TESTER_WRAPPER_TAG, array('priority' => 1000));
        $container->setDefinition(TesterExtension::SPECIFICATION_TESTER_WRAPPER_TAG . '.per_variant_branch', $definition);
    }

    /**
     * Loads step tester.
     *
     * @param ContainerBuilder $container
     */
    protected function loadStepTester(ContainerBuilder $container, $variantTags)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Tester\PlaceholdersReplacer', array(
            new Reference(TesterExtension::STEP_TESTER_ID),
            $variantTags,
            new Reference(ConfigsRepository::CONFIGS_REPOSITORY_ID)
        ));
        $definition->addTag(TesterExtension::STEP_TESTER_WRAPPER_TAG);
        $container->setDefinition(self::PLACEHOLDER_REPLACER_ID, $definition);
    }
}
