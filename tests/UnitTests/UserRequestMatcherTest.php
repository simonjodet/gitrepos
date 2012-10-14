<?php
namespace Tests\UnitTests;
class UserRequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_matches_allows_login_route()
    {
        $UserRequestMatcher = new \Gitrepos\UserRequestMatcher();
        $RequestMock = \Mockery::mock('\Symfony\Component\HttpFoundation\Request');
        $RequestMock
            ->shouldReceive('getRequestUri')
            ->andReturn('/login');
        $this->assertFalse($UserRequestMatcher->matches($RequestMock));
    }

    public function test_matches_allows_signin_route()
    {
        $UserRequestMatcher = new \Gitrepos\UserRequestMatcher();
        $RequestMock = \Mockery::mock('\Symfony\Component\HttpFoundation\Request');
        $RequestMock
            ->shouldReceive('getRequestUri')
            ->andReturn('/signin');
        $this->assertFalse($UserRequestMatcher->matches($RequestMock));
    }

    public function test_matches_does_not_allow_other_routes()
    {
        $UserRequestMatcher = new \Gitrepos\UserRequestMatcher();
        $RequestMock = \Mockery::mock('\Symfony\Component\HttpFoundation\Request');
        $RequestMock
            ->shouldReceive('getRequestUri')
            ->andReturn('/other/route', '/username/other/route');
        $this->assertTrue($UserRequestMatcher->matches($RequestMock));
        $this->assertTrue($UserRequestMatcher->matches($RequestMock));
    }
}
