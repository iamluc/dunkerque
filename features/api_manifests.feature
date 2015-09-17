Feature: Test manifests endpoint

  Background:
    Given I set basic authentication with "test" and "test"

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password |
      | test     | test     |
      | test_KO  | 12345    |
    And the following manifests:
      | name        | tag      | digest | content            |
      | hello-world | 2.4      | aaa    | {"test": "ok"}     |
      | hello-world | latest   | bbb    | {"test": "latest"} |

  Scenario: Test get manifest with invalid image name
    When I send a "GET" request to "/v2/test-image/manifests/test-reference"
    Then the response status code should be 404
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"

  Scenario: Test get manifest with valid image name
    When I send a "GET" request to "/v2/hello-world/manifests/latest"
    Then the response status code should be 200
    And the response should be equal to
      """
      {"test": "latest"}
      """
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"
    And the header "Content-Type" should contain "application/json"
