Feature: sessions_delete
  In order to have a secured experience with Gitrepos
  As a user
  I need to be able to invalidate my session

  Scenario: Logout
    Given that I'm logged in as "simon" "azeaze"
    When I log out
    Then the response code should be "200"
    And the headers  should match the following regexp:
    """
/.*Set-Cookie: SESSION=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=\/.*/
    """
    And I log out
    And the response code should be "401"
    And the body string should be:
    """
{"code":401,"message":"Requires authentication","doc":"\/docs\/sessions.json"}
    """

  Scenario: Logout without session fails
    When I log out
    Then the response code should be "401"
    And the body string should be:
    """
{"code":401,"message":"Requires authentication","doc":"\/docs\/sessions.json"}
    """

