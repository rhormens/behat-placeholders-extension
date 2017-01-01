Feature: Variant Branching
  In order to avoid redundancy
  As a feature writer
  I need to run the same scenario with different
    parameters for different variants of the application

  Background:
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php
      use Behat\Behat\Context\Context;

      class FeatureContext implements Context
      {
        /**
         * @Given /^the ice cream truck has "(?P<flavor>[^"]*)" ice cream$/
         */
        public function echoFlavor($flavor) {
            echo "It sells $flavor ice cream";
        }
      }
      """
    And a file named "behat.yml" with:
      """
      default:
          extensions:
              Ciandt\Behat\PlaceholdersExtension:
                  variant_tags:
                      - vanilla
                      - chocolate
                      - pistachio
                  config_tags:
                      ice_cream: %paths.base%/features/ice_cream.yml
      """
    And a file named "features/ice_cream.yml" with:
      """
      default: 
        placeholders:
          flavor:
            $default:
              $vanilla: vanilla
              $chocolate: chocolate
              $pistachio: pistachio
      """
      

    Scenario: Single Variant Scenario
        Given a file named "features/single_flavor.feature" with:
          """
          Feature: Single flavor
            @ice_cream @vanilla
            Scenario: Offer a single ice cream flavor
              Given the ice cream truck has "${flavor}" ice cream
          """

        When I run "behat features/single_flavor.feature"
        Then it should pass with:
          """
            @ice_cream @vanilla
            Scenario: Offer a single ice cream flavor             # features/single_flavor.feature:3
              Given the ice cream truck has "${flavor}" ice cream # FeatureContext::echoFlavor()
                │ It sells vanilla ice cream

          1 scenario (1 passed)
          1 step (1 passed)
          """

    Scenario: Multi variant scenario branching
        Given a file named "features/multiple_flavors.feature" with:
          """
          Feature: Multiple flavors
            @ice_cream @vanilla @chocolate @pistachio
            Scenario: Offer several ice cream flavors
              Given the ice cream truck has "${flavor}" ice cream
          """

        When I run "behat features/multi_variant.feature"
        Then it should pass with:
          """
            @ice_cream @vanilla
            Scenario: Echo placeholder value # features/multi_variant.feature:3
              Given I echo "${flavor}"       # FeatureContext::echoString()
                │ vanilla

            @ice_cream @chocolate
            Scenario: Echo placeholder value # features/multi_variant.feature:3
              Given I echo "${flavor}"       # FeatureContext::echoString()
                │ chocolate

            @ice_cream @pistachio
            Scenario: Echo placeholder value # features/multi_variant.feature:3
              Given I echo "${flavor}"       # FeatureContext::echoString()
                │ pistachio

          3 scenarios (3 passed)

         """
    Scenario: Multi variant scenario branching with non-variant tags
        Given a file named "features/multi_variant.feature" with:
          """
          Feature: Multi Variant
            @ice_cream @vanilla @chocolate @other
            Scenario: Echo placeholder value
              Given I echo "${flavor}"
          """

        When I run "behat features/multi_variant.feature"
        Then it should pass with:
          """
            @ice_cream @other @vanilla
            Scenario: Echo placeholder value # features/multi_variant.feature:3
              Given I echo "${flavor}"       # FeatureContext::echoString()
                │ vanilla

            @ice_cream @other @chocolate
            Scenario: Echo placeholder value # features/multi_variant.feature:3
              Given I echo "${flavor}"       # FeatureContext::echoString()
                │ chocolate

          2 scenarios (2 passed)

         """