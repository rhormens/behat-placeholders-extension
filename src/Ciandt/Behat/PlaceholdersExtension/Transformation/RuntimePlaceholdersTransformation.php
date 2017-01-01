<?php


namespace Ciandt\Behat\PlaceholdersExtension\Transformation;

use Behat\Behat\Definition\Call\DefinitionCall;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Call\RuntimeCallee;
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;
use Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer\PlaceholderContainer;
use Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer\PlaceholderContainerStepNode;

/**
 *
 */
final class RuntimePlaceholdersTransformation extends RuntimeCallee implements PlaceholdersTransformation {

    const USER_DEFINED_PLACEHOLDER_REGEX = '/\${(?P<placeholder>[a-zA-Z0-9_-]+)}/';

    public function __toString() {
        return 'UserDefinedPlaceholderTransform';
    }

    public function getPattern() {
        return self::USER_DEFINED_PLACEHOLDER_REGEX;
    }

    private static function replaceStringPlaceholders($string, PlaceholdersRepository $repository, PlaceholderContainerStepNode $container) {
        preg_match_all(self::USER_DEFINED_PLACEHOLDER_REGEX, $string, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $placeholder = $match['placeholder'];
            $replacement = $repository->getReplacement($placeholder, $container);
            $string = str_replace('${' . $placeholder . '}', $replacement, $string);
        }
        return $string;
    }

    private static function replaceTablePlaceholders(TableNode $table, PlaceholdersRepository $repository, PlaceholderContainerStepNode $container) {
        
    }

    public static function supportsDefinitionAndArgument(DefinitionCall $definitionCall, $argumentValue) {
        if ($definitionCall->getStep() instanceof PlaceholderContainerStepNode) {
            if (is_string($argumentValue) && preg_match(self::USER_DEFINED_PLACEHOLDER_REGEX, $argumentValue) === 1) {
                return true;
            }
            if ($argumentValue instanceof TableNode && $this->tableHasPlaceholders($table)) {
                return true;
            }
        }
        return false;
    }

    private static function tableHasPlaceholders(TableNode $table) {
        return (preg_match(self::USER_DEFINED_PLACEHOLDER_REGEX, $table->getTableAsString()) === 1);
    }

    public static function transformArgument($argumentValue, PlaceholdersRepository $repository, PlaceholderContainer $container) {
        if (is_string($argumentValue)) {
            return self::replaceStringPlaceholders($argumentValue, $repository, $container);
        }

        if ($argumentValue instanceof TableNode) {
            return self::replaceTablePlaceholders($table, $repository, $container);
        }
    }

}
