Feature: Test version endpoint

  Scenario: As an anonymous user, I cannot access version endpoint
    Given I send a "GET" request to "/v2/"
    Then the response status code should be 401
    And the response should be in JSON
    And the JSON node "error.code" should be equal to "UNAUTHORIZED"
    And the JSON node "error.message" should be equal to "access to the requested resource is not authorized"
    And the header "WWW-Authenticate" should contain 'Bearer realm="http'
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"

  @reset-schema
  Scenario: As a user with valid credentials, I can access version endpoint
    Given I have users:
      | username | password |
      | test     | test     |
    When I set basic authentication with "test" and "test"
    And I send a "GET" request to "/v2/"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be equal to:
    """
    {}
    """
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"

  Scenario: As a user with invalid credentials, I cannot access version endpoint
    Given I set basic authentication with "test_KO" and "test1234"
    And I send a "GET" request to "/v2/"
    Then the response status code should be 401
    And the response should be in JSON
    And the JSON node "error.code" should be equal to "UNAUTHORIZED"
    And the JSON node "error.message" should be equal to "access to the requested resource is not authorized"
    And the header "WWW-Authenticate" should contain "Bearer"
    And the header "Docker-Distribution-Api-Version" should contain "registry/2.0"
