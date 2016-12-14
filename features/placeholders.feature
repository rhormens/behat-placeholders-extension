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