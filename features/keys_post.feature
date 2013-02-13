Feature: keys_post
  In order to clone Git repositories with the SSH protocol
  As a user
  I need to be able to register my SSH keys

  Scenario: Add an SSH key
    Given that I'm logged in as "simon" "azeaze"
    And my key title is "test key 1"
    And my key value is:
    """
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDyXJOUHlSUtiZeR/AJ9Riu5LVUXdt2BsKeUF4r1hXQC7aeLtXdCzGwTlRodnBNAiYguqAYnaCFRBvWZHjRrRwherJ6fsTnNqwhYqWzEnuc86x30JmlfF4Hm3ZZvLJXblQu3tUqx4xcRGd0+Tm9UaDR+Pj7ioGzpzIBf9hOb6yLJ/qJ6lW4/W/QMAHS7mkL2cknIpNxuCx2SS2HJSKhX8yGYmdtO9cCbuggnsd37aY9W/gKio7kbOd914Mw1PWd//7b3V3IoXsDEDwcoVKca1AriSe1YOHCImLVxD7vjy7IRb/vybX4XxRd5Qd65OdYHAyDb/UTJmsbtThFIuxNWGJl test@example.com
    """
    When I add the key
    Then the response code should be "201"
    And the body string should match the following regexp:
    """
/{"id":[0-9]*,"title":"test key 1","value":"ssh\-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDyXJOUHlSUtiZeR\\\/AJ9Riu5LVUXdt2BsKeUF4r1hXQC7aeLtXdCzGwTlRodnBNAiYguqAYnaCFRBvWZHjRrRwherJ6fsTnNqwhYqWzEnuc86x30JmlfF4Hm3ZZvLJXblQu3tUqx4xcRGd0\+Tm9UaDR\+Pj7ioGzpzIBf9hOb6yLJ\\\/qJ6lW4\\\/W\\\/QMAHS7mkL2cknIpNxuCx2SS2HJSKhX8yGYmdtO9cCbuggnsd37aY9W\\\/gKio7kbOd914Mw1PWd\\\/\\\/7b3V3IoXsDEDwcoVKca1AriSe1YOHCImLVxD7vjy7IRb\\\/vybX4XxRd5Qd65OdYHAyDb\\\/UTJmsbtThFIuxNWGJl test@example\.com"}/
    """

  Scenario: Adding an SSH key without session fails
    Given my key title is "test key 1"
    And my key value is:
    """
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDyXJOUHlSUtiZeR/AJ9Riu5LVUXdt2BsKeUF4r1hXQC7aeLtXdCzGwTlRodnBNAiYguqAYnaCFRBvWZHjRrRwherJ6fsTnNqwhYqWzEnuc86x30JmlfF4Hm3ZZvLJXblQu3tUqx4xcRGd0+Tm9UaDR+Pj7ioGzpzIBf9hOb6yLJ/qJ6lW4/W/QMAHS7mkL2cknIpNxuCx2SS2HJSKhX8yGYmdtO9cCbuggnsd37aY9W/gKio7kbOd914Mw1PWd//7b3V3IoXsDEDwcoVKca1AriSe1YOHCImLVxD7vjy7IRb/vybX4XxRd5Qd65OdYHAyDb/UTJmsbtThFIuxNWGJl test@example.com
    """
    When I add the key
    Then the response code should be "401"
    And the body string should be:
    """
{"code":401,"message":"Requires authentication","doc":"\/docs\/keys.json"}
    """