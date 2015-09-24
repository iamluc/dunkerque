Feature: Test manifests endpoint

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password | roles      |
      | admin    | admin    | ROLE_ADMIN |
      | test     | test     | ROLE_USER  |
    And the following repositories:
      | name             | owner |
      | hello-world      | admin |
      | test/hello-world | test  |
    And I have manifests:
      | hello-world:latest.json      |
      | test~hello-world:latest.json |

  Scenario: As a simple user, access unknown manifest from my namespace
    Given I set basic authentication with "test" and "test"
    When I send a "GET" request to "/v2/test/goodbye-world/manifests/latest"
    Then the response status code should be 404
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"

  Scenario: As a simple user, access existing manifest from my namespace
    Given I set basic authentication with "test" and "test"
    When I send a "GET" request to "/v2/test/hello-world/manifests/latest"
    Then the response status code should be 200
    And the response should be equal to file "manifests/test~hello-world:latest.json"
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"
    And the header "Content-Type" should contain "application/json"

  Scenario: As a simple user, access private manifest from the root namespace
    Given I set basic authentication with "test" and "test"
    When I send a "GET" request to "/v2/hello-world/manifests/latest"
    Then the response status code should be 403
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"

  Scenario: As an admin, access private manifest from the root namespace
    Given I set basic authentication with "admin" and "admin"
    When I send a "GET" request to "/v2/hello-world/manifests/latest"
    Then the response status code should be 200
    And the response should be equal to file "manifests/hello-world:latest.json"
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"
