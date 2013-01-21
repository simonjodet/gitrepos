Feature: users_create_account
  In order to use Gitrepos features
  As a user
  I need to be able to create a user account

  Scenario: Create an account
    Given that I want to create a new "simon" user
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "201"
    And the body should be ""

  Scenario: Check for short username
    Given that I want to create a new "sy" user
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "400"
    And the body string should be:
    """
{"code":400,"message":"Invalid username","doc":"\/docs\/users.json"}
    """