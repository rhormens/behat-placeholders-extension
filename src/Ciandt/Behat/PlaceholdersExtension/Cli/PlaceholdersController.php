<?php
namespace Ciandt\Behat\PlaceholdersExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ciandt\Behat\PlaceholdersExtension\Tester\PlaceholdersReplacer;

final class PlaceholdersController implements Controller
{

    private $placeholderReplacer;

    function __construct(PlaceholdersReplacer $placeholderReplacer)
    {
        $this->placeholderReplacer = $placeholderReplacer;
    }

    /**
     * Adds the optional --environment / -e option to the Behat CLI
     *
     * @param SymfonyCommand $command
     */
    public function configure(SymfonyCommand $command)
    {
        $command->addOption('environment', 'e', InputArgument::OPTIONAL, 'Set the environment to run the features', 'default');
    }

    /**
     * Gets the environment option and pass it on to the PlaceholdersReplacer
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @todo pass environment to StepTester
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('environment')) {
            $this->placeholderReplacer->setEnvironment($input->getOption('environment'));
        }
    }
}
