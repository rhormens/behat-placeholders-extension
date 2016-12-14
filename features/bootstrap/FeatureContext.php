<?php

use Behat\Behat\Context\Context;

class FeatureContext implements Context {

    
    /**
     * Echo string
     * @When /^I echo "(?P<string>[^"]*)"$/
     */
    public function echoString($string) {
        echo $string;
    }

}
