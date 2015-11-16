Feature: Test homepage

  Scenario: Test homepage is correct
    Given I am on the homepage
    Then the response status code should be 200
    Then I should see "Dunkerque"

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password | roles      |
      | admin    | admin    | ROLE_ADMIN |
      | test     | test     | ROLE_USER  |
    And the following repositories:
      | name              | owner | private | pulls |
      | hello-world       | admin | false   | 9876  |
      | secret-world      | admin | true    | 0     |
      | test/hello-world  | test  | false   | 42    |
      | test/secret-world | test  | true    | 654   |
    When I am on the homepage
    Then I should see "hello-world"
    And I should see "Most pulled"
    And I should see "9876"
    And I should not see "secret-world"
