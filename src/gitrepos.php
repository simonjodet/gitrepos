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
    '/',
    function (\Silex\Application $app)
    {
        return 'List of repositories for user ' . $app['security']->getToken()->getUsername();
    });

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
    '/signin',
    function (\Silex\Application $app)
    {
        return 'Registration page';
    });

$app->post(
    '/signin',
    function (\Silex\Application $app)
    {
        return 'Registration';
    });

$app->get(
    '/add',
    function (\Silex\Application $app)
    {
        return 'Create repository form for user ' . $app['security']->getToken()->getUsername();
    });

$app->post(
    '/add',
    function (\Silex\Application $app)
    {
        return 'Create repository for user ' . $app['security']->getToken()->getUsername();
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
    '/{username}/{reponame}/edit',
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