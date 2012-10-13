<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request,
    \Silex\Provider\FormServiceProvider;

$app = new \Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new \Silex\Provider\SecurityServiceProvider());
$app['security.firewalls'] = array(
    'user_firewall' => array(
        'pattern' => new \Gitrepos\UserRequestMatcher($app['request']),
        'form' => array('login_path' => '/login', 'check_path' => '/authenticate'),
        'users' => array(
            // raw password is foo
            'admin' => array('ROLE_USER', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
        ),
    ),
);

$app->get(
    '/login',
    function(Request $request) use ($app)
    {
        return $app['twig']->render('login.twig', array(
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    });

$app->get(
    '/',
    function (\Silex\Application $app)
    {
        return 'Home';
    });

$app->get(
    '/register',
    function (\Silex\Application $app)
    {
        return 'Registration page';
    });

$app->get(
    '/{username}/',
    function (\Silex\Application $app, $username)
    {
        return 'List of repositories for user ' . $username;
    });

$app->get(
    '/{username}/add',
    function (\Silex\Application $app, $username)
    {
        return 'Create repository form for user ' . $username;
    });

$app->post(
    '/{username}/create',
    function (\Silex\Application $app, $username)
    {
        return 'Create repository for user ' . $username;
    });

$app->get(
    '/{username}/{reponame}/',
    function (\Silex\Application $app, $username, $reponame)
    {
        return 'Details for  ' . $username . '/' . $reponame;
    });

$app->get(
    '/{username}/{reponame}/edit',
    function (\Silex\Application $app, $username, $reponame)
    {
        return 'Repository edition form for  ' . $username . '/' . $reponame;
    });

$app->post(
    '/{username}/{reponame}/update',
    function (\Silex\Application $app, $username, $reponame)
    {
        return 'Repository edition for  ' . $username . '/' . $reponame;
    });

$app->post(
    '/{username}/{reponame}/delete',
    function (\Silex\Application $app, $username, $reponame)
    {
        return 'Repository deletion of  ' . $username . '/' . $reponame;
    });

return $app;