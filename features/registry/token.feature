Feature: Test token endpoint

  Scenario: As an anonymous user, I can access token endpoint
    Given I send a "GET" request to "/token"
    Then the response status code should be 200
    And the JSON node "token" should exist
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"
