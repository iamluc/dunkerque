Feature: Test repository description page

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password | roles      |
      | admin    | admin    | ROLE_ADMIN |
      | test     | test     | ROLE_USER  |
    And the following repositories:
      | name        | owner | private | stars | pulls | description              |
      | hello-world | admin | true    | 3     | 12    |                          |
      | dummy       | admin | false   | 0     | 0     | Dunkerque dummy image    |
      | dunkerque   | admin | false   | 1000  | 234   | Dunkerque official image |
    And the following repositoryStars:
      | user  | repository |
      | test  | dunkerque  |
      | admin | dunkerque  |
    And I have manifests:
      | hello-world:latest.json |

  Scenario: I go to a non-existing repository
    Given I go to "/r/admin/docker"
    Then the response status code should be 404

  Scenario: As an anonymous user, I go to a private repository
    Given I go to "/r/hello-world"
    Then the response status code should be 200
    And I should be on "/login"

  Scenario: As an anonymous user, I go to a public repository
    Given I go to "/r/dunkerque"
    Then the response status code should be 200
    And I should see "Dunkerque official image"
    And I should not see an "a#repository-star" element

  Scenario: As an authenticated user, I go to a public repository
    Given I am authenticated as "test"
    And I go to "/r/dunkerque"
    Then I should see an "a#repository-star" element
    And I should see an "span.glyphicon.glyphicon-star" element
    Given I go to "/r/dummy"
    Then I should see an "a#repository-star" element
    And I should see an "span.glyphicon.glyphicon-star-empty" element

  Scenario: As an authenticated user, I go to a private repository
    Given I am authenticated as "test"
    And I go to "/r/hello-world"
    Then the response status code should be 403
