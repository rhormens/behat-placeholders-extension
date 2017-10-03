Feature: Runtime Placeholders
  In order to make the test suite more flexible
  As a Context writer
  I need to use placeholders on my steps and hooks

  Background:
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php
      use Behat\Behat\Context\Context;
      use Ciandt\Behat\PlaceholdersExtension\Initializer\PlaceholdersAwareInterface;
      use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;

      class FeatureContext implements Context, PlaceholdersAwareInterface
      {

         public function setPlaceholdersRepository(PlaceholdersRepository $repository)
         {
            $this->placeholders = $repository;
         }
          
        /**
         * @Given I like ":flavor" ice cream
         */
        public function gotIceCream($flavor) {
            $this->placeholders->setReplacement("flavor",$flavor);
        }
          
        /**
         * @Given the ice cream truck has ":flavor" ice cream
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
              Ciandt\Behat\PlaceholdersExtension: ~
      """

    @test
    Scenario: Using runtime placeholders across scenarios
        Given a file named "features/runtime_placeholder.feature" with:
          """
          Feature: Using runtime placeholders across scenarios
            Scenario: I like a specific ice cream flavor
              Given I like "vanilla" ice cream

            Scenario: The ice cream truck sells that flavor
              Given the ice cream truck has "${flavor}" ice cream
          """

        When I run "behat features/runtime_placeholder.feature"
        Then it should pass with:
          """
            Scenario: The ice cream truck sells that flavor       # features/runtime_placeholder.feature:5
              Given the ice cream truck has "${flavor}" ice cream # FeatureContext::echoFlavor()
                │ It sells vanilla ice cream
          """

