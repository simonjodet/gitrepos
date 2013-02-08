Feature: sessions_post
  In order to use Gitrepos features
  As a user
  I need to be able to create a user session

  Scenario: Create a session with username
    Given that I want to create a new "simonjodet" user
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I create the account
    And I want to login with "simonjodet" as identifier
    Then the response code should be "200"
    And the headers  should match the following regexp:
    """
/.*Set-Cookie: SESSION=\w*; path=\/.*/
    """
    And the body string should match the following regexp:
    """
/{"session":"\w*"}/
    """

  Scenario: Fail to create a session with bad username
    Given that I want to create a new "simonjodet" user
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I create the account
    And I want to login with "not the correct username" as identifier
    Then the response code should be "401"
    And the body string should be:
    """
{"code":401,"message":"Bad credentials","doc":"\/docs\/sessions.json"}
    """

  Scenario: Fail to create a session with bad password
    Given that I want to create a new "simonjodet" user
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I create the account
    And I want to login with "not the correct password" as password
    Then the response code should be "401"
    And the body string should be:
    """
{"code":401,"message":"Bad credentials","doc":"\/docs\/sessions.json"}
    """

