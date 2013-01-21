Feature: sessions_login
  In order to use Gitrepos features
  As a user
  I need to be able to create a user session

  Scenario: Create a session
    Given that I want to create a new "simonjodet" user
    And his password is "azeaze"
    And his email is "nobody@example.com"
    When I create the account
    And I want to login with "simonjodet" as identifier
    And I request the URL "/v1/sessions" with the POST method
    Then the response code should be "230"
    And the body string should match the following regexp:
    """
/{"session":"\w*","ttl":"\d*"}/
    """