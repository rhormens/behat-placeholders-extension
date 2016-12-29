@placeholders:test
Feature: Test if the Placeholders Extension is working correctly

  # This scenario should run three times, one for each of the tags @br, @us and @ca
  # each time replacing ${placeholder1} and ${placeholder2} with the corresponding values
  # that are set on the features/placeholders.yml file.
  #
  # Tags that do not correspond to a variant or a placeholders config file should
  # be kept on all executions of this scenario.
  @placeholders @br @us @ca @other
  Scenario: Fork scenarios with multiple variants
    When I echo "${placeholder1} and ${placeholder2}"

  #this scenario should run only once, replacing the placeholders correctly
  @placeholders @br @other
  Scenario: Run scenario with single variant
    When I echo "${placeholder1} and ${placeholder2}"

  # this scenario should use a different placeholder value depending on the
  # --environment/-e parameter provided on the command line
  @placeholders
  Scenario: Use environment dependant placeholder
    When I echo "the environment-dependant value is: ${depends_on_env}"
  
  # this scenario should use the placeholder from the "alternative" section
  # defined on features/placeholders.yml:alternative>placeholders>alternative_placeholder
  @placeholders:alternative
  Scenario: Use another placeholders file section
    When I echo "${alternative_placeholder}"