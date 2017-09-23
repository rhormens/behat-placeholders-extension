Feature: Malformed features
  In order to make features development easier
  As a feature writer
  I must be able to leave empty Feature files on my test suite

    Background:
      Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php
      use Behat\Behat\Context\Context;
      class FeatureContext implements Context {
        /**
         * @Given I do nothing
         */
         public function nothing(){}
      }
      """
      And a file named "behat.yml" with:
      """
      default:
          extensions:
              Ciandt\Behat\PlaceholdersExtension: ~
      """
      And a file named "features/valid.feature" with:
      """
      Feature: This is a valid feature
        Scenario: This is a valid scenario
          Given I do nothing
      """

     Scenario: Empty feature is ignored
        Given a file named "features/empty.feature" with:
          """
          """

        When I run "behat"
        Then it should pass with:
          """
          1 step (1 passed)
          """