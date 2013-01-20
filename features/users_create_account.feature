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
