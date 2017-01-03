<?php
namespace Ciandt\Behat\PlaceholdersExtension\ServiceContainer;

use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Behat\Transformation\ServiceContainer\TransformationExtension;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Ciandt\Behat\PlaceholdersExtension\Utils\PlaceholderUtils;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class PlaceholdersExtension implements Extension
{

    const PLACEHOLDERS_REPLACER_ID = 'placeholders.replacer';
    const PLACEHOLDERS_REPOSITIORY_ID = 'placeholders.repository';
    const PLACEHOLDERS_CONTROLLER_ID = 'placeholders.controller';
    const PLACEHOLDERS_TRANSFORMER_ID = 'placeholders.transformer';
    const VARIANTS_PREPROCESSOR_ID = 'placeholders.variants_preprocessor';
    const STEPS_DECORATOR_ID = 'placeholders.steps_decorator';
    const BEFORE_SCENARIO_SUBSCRIBER_ID = 'placeholders.before_scenario';

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
        $this->initializePlaceholderUtils($config['variant_tags'], $config['config_tags']);
        $this->loadScenarioBranchingFeatureTester($container, $config['variant_tags']);
        $this->loadPlaceholdersRepository($container, $config['config_tags']);
        $this->loadPlaceholdersController($container);
        $this->loadBeforeScenarioSubscriber($container);
        $this->loadPlaceholdersTransformer($container);
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

    protected function initializePlaceholderUtils($variantTags,$configTags){
        PlaceholderUtils::setVariantTags($variantTags);
        PlaceholderUtils::setConfigKeys(array_keys($configTags));
    }


    /**
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
     *
     * @param ContainerBuilder $container
     */
    protected function loadScenarioBranchingFeatureTester(ContainerBuilder $container, $variantTags)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Tester\ScenarioBranchingFeatureTester', array(
            new Reference(TesterExtension::SPECIFICATION_TESTER_ID),
            new Reference(self::PLACEHOLDERS_REPOSITIORY_ID)
        ));
        $definition->addTag(TesterExtension::SPECIFICATION_TESTER_WRAPPER_TAG, array('priority' => 1000));
        $container->setDefinition(self::VARIANTS_PREPROCESSOR_ID, $definition);
    }


    /**
     * Loads transformers.
     *
     * @param ContainerBuilder $container
     */
    protected function loadPlaceholdersTransformer(ContainerBuilder $container)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Transformer\PlaceholdersTransformer', array(
            new Reference(self::PLACEHOLDERS_REPOSITIORY_ID),
            new Reference(self::BEFORE_SCENARIO_SUBSCRIBER_ID)
        ));
        $definition->addTag(TransformationExtension::ARGUMENT_TRANSFORMER_TAG, array('priority' => 9999999));
        $container->setDefinition(self::PLACEHOLDERS_TRANSFORMER_ID, $definition);
    }
    

    protected function loadStepDecoratingScenarioTester(ContainerBuilder $container)
    {
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Tester\StepDecoratingScenarioTester', array(
            new Reference(TesterExtension::SCENARIO_TESTER_ID)
        ));
        $definition->addTag(TesterExtension::SCENARIO_TESTER_WRAPPER_TAG, array('priority' => 9999999));
        $container->setDefinition(self::STEPS_DECORATOR_ID, $definition);
    }
    
    protected function loadBeforeScenarioSubscriber(ContainerBuilder $container){
        $definition = new Definition('Ciandt\Behat\PlaceholdersExtension\Subscriber\BeforeScenarioSubscriber');
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition(self::BEFORE_SCENARIO_SUBSCRIBER_ID, $definition);
    }
}
