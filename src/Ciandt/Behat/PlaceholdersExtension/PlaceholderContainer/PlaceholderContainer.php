<?php


namespace Ciandt\Behat\PlaceholdersExtension\PlaceholderContainer;


/**
 * Description of PlaceholderContainer
 *
 * @author bwowk
 */
interface PlaceholderContainer {
    
public function getVariant();

public function getConfigKey();

public function getSectionKey();
    
}
