Feature: users_create_account
  In order to use Gitrepos features
  As a user
  I need to be able to create a user account

  Scenario: Create an account
    Given that I want to create a new "simonjodet1" user
    And his password is "azeaze"
    And his email is "nobody1@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "201"
    And the body should be ""

  Scenario: Check for short username
    Given that I want to create a new user with a username shorter than 3 characters
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "400"
    And the body string should be:
    """
{"code":400,"message":"Invalid username","doc":"\/docs\/users.json"}
    """

  Scenario: Check for long username
    Given that I want to create a new user with a username longer than 64 characters
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "400"
    And the body string should be:
    """
{"code":400,"message":"Invalid username","doc":"\/docs\/users.json"}
    """

  Scenario: Check for invalid email
    Given that I want to create a new "simon" user
    And his password is "azeaze"
    And his email is "not_an_email.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "400"
    And the body string should be:
    """
{"code":400,"message":"Invalid email","doc":"\/docs\/users.json"}
    """

  Scenario: Check for short password
    Given that I want to create a new "simon" user
    And his password is shorter than 6 characters
    And his email is "nobody@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "400"
    And the body string should be:
    """
{"code":400,"message":"Invalid password","doc":"\/docs\/users.json"}
    """

  Scenario: Check for long password
    Given that I want to create a new "simon" user
    And his password is longer than 128 characters
    And his email is "nobody@example.com"
    When I request the URL "/v1/users" with the POST method
    Then the response code should be "400"
    And the body string should be:
    """
{"code":400,"message":"Invalid password","doc":"\/docs\/users.json"}
    """

  Scenario: Check for duplicate username
    Given that I want to create a new "simonjodet2" user
    And his password is "azeaze"
    And his email is "nobody2@example.com"
    When I request the URL "/v1/users" with the POST method
    And his email is "nobody3@example.com"
    And I request the URL "/v1/users" with the POST method again
    Then the response code should be "409"
    And the body string should be:
    """
{"code":409,"message":"This username is already used","doc":"\/docs\/users.json"}
    """

  Scenario: Check for duplicate email
    Given that I want to create a new "simonjodet2" user
    And his password is "azeaze"
    And his email is "nobody2@example.com"
    When I request the URL "/v1/users" with the POST method
    And his email is "nobody2@example.com"
    And I request the URL "/v1/users" with the POST method again
    Then the response code should be "409"
    And the body string should be:
    """
{"code":409,"message":"This email is already used","doc":"\/docs\/users.json"}
    """
