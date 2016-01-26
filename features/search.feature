Feature: Test search

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password | roles      |
      | admin    | admin    | ROLE_ADMIN |
      | test     | test     | ROLE_USER  |
    And the following repositories:
      | name            | owner | private | stars | pulls | description                      |
      | not_found       | admin | false   | 3     | 12    | Not found                        |
      | admin_public_1  | admin | false   | 3     | 12    | Dunkerque official image         |
      | admin_public_2  | admin | false   | 0     | 0     | Dunkerque almost official image  |
      | admin_private_1 | admin | true    | 1000  | 234   | Dunkerque |
      | test_private_1  | test  | true    | 1000  | 234   | Dunkerque |
    And Entities are indexed

  Scenario: I search a non-existing repository
    Given I go to "/"
    And I fill in "search_keyword" with "unknown"
    And I press "search_submit"
    Then the response status code should be 200
    And I should see "No result found for \"unknown\""
    And I should not see an "table" element

  Scenario: I search public repositories
    Given I go to "/"
    And I fill in "search_keyword" with "Dunkerque"
    And I press "search_submit"
    Then the response status code should be 200
    And I should see "2 results found for \"Dunkerque\""
    And I should see the following table:
      | Name            | Stars | Pulls | Private |
      | admin_public_1  | 3     | 12    | Public  |
      | admin_public_2  | 0     | 0     | Public  |

  Scenario: As a user, I search public and private repositories
    Given I am authenticated as "test"
    And I go to "/"
    And I fill in "search_keyword" with "Dunkerque"
    And I press "search_submit"
    Then the response status code should be 200
    And I should see "3 results found for \"Dunkerque\""
    And I should see the following table:
      | Name            | Stars | Pulls | Private |
      | test_private_1  | 1000  | 234   | Private |
      | admin_public_1  | 3     | 12    | Public  |
      | admin_public_2  | 0     | 0     | Public  |
