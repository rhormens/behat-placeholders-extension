<?php
namespace Ciandt\Behat\PlaceholdersExtension\ServiceContainer;

use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;
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

    const PLACEHOLDERS_REPLACER_ID = 'placeholders.replacer';
    const PLACEHOLDERS_REPOSITIORY_ID = 'placeholders.repository';
    const PLACEHOLDERS_CONTROLLER_ID = 'placeholders.controller';
    const VARIANTS_PREPROCESSOR_ID = 'placeholders.variants_preprocessor';

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
            ->arrayNode('config_tags')
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
        $this->loadPlaceholdersRepository($container, $config['config_tags']);
        $this->loadStepTester($container, $config['variant_tags'], 'default');
        $this->loadPlaceholdersController($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadPlaceholdersController(ContainerBuilder $container)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Cli\PlaceholdersController', array(
            new Reference(self::PLACEHOLDERS_REPOSITIORY_ID)));
        $definition->addTag(CliExtension::CONTROLLER_TAG, array('priority' => 1));
        $container->setDefinition(self::PLACEHOLDERS_CONTROLLER_ID, $definition);
    }

    /**
     * Loads event-dispatching feature tester.
     *
     * @param ContainerBuilder $container
     */
    protected function loadPlaceholdersRepository(ContainerBuilder $container, $configs_mapping)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository', array(
            $configs_mapping
        ));
        $container->setDefinition(self::PLACEHOLDERS_REPOSITIORY_ID, $definition);
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
            new Reference(self::PLACEHOLDERS_REPOSITIORY_ID)
        ));
        $definition->addTag(TesterExtension::SPECIFICATION_TESTER_WRAPPER_TAG, array('priority' => 1000));
        $container->setDefinition(self::VARIANTS_PREPROCESSOR_ID, $definition);
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
            new Reference(self::PLACEHOLDERS_REPOSITIORY_ID)
        ));
        $definition->addTag(TesterExtension::STEP_TESTER_WRAPPER_TAG);
        $container->setDefinition(self::PLACEHOLDERS_REPLACER_ID, $definition);
    }
}
