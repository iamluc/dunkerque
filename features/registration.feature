Feature: Test registration

  Scenario: I can access the registration page
    Given I go to "/register/"
    Then the response status code should be 200
