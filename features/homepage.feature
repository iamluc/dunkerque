Feature: Test homepage

  @reset-schema
  Scenario: Test homepage is correct
    Given I am on homepage
    Then the response status code should be 200
    Then I should see "Dunkerque"
