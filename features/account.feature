Feature: Test account

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password | roles      |
      | admin    | admin    | ROLE_ADMIN |
      | test     | test     | ROLE_USER  |
    And the following repositories:
      | name        | owner | private | stars | pulls |
      | hello-world | admin | false   | 3     | 12    |
    And I have manifests:
      | hello-world:latest.json |

  Scenario: I go to a non-existing account
    Given I go to "/u/john"
    Then the response status code should be 404

  Scenario: I go to a existing account
    Given I go to "/u/admin"
    Then the response status code should be 200
    And I should see a table with 1 row
    And I should see the following table:
      | Name        | Stars | Pulls | Private |
      | hello-world | 3     | 12    | Public  |

  Scenario: I am authenticated
    Given I am authenticated as "test"
    And I go to "/"
    Then the response status code should be 200
    And I should see "test"
    And I should see "Log out"
