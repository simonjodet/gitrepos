<?php
require_once __DIR__ . '/../vendor/autoload.php';

use
\Symfony\Component\HttpFoundation\Request,
\Symfony\Component\Form\FormError,
\Symfony\Component\Validator\Constraints as Assert,
\Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
\Silex\Provider\FormServiceProvider;

$app = new \Silex\Application();
$Configuration = new \Gitrepos\Configuration(__DIR__ . '/../conf/conf.json');
if (!$env = getenv('APP_ENV'))
{
    $env = null;
}
$app['conf'] = $Configuration->get($env);
$app['debug'] = $app['conf']['app.debug'];

date_default_timezone_set($app['conf']['timezone']);

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.name' => 'GR',
    'monolog.logfile' => $app['conf']['monolog.logfile'],
    'monolog.level' => constant('\Monolog\Logger::' . $app['conf']['monolog.level'])
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views'
));

$app->register(new FormServiceProvider(), array(
    'form.secret' => $app['conf']['form.secret']
));

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback' => $app['conf']['translation.locale_fallback']
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new \Silex\Provider\SecurityServiceProvider());
$app['security.firewalls'] = array(
    'user_firewall' => array(
        'pattern' => new \Gitrepos\UserRequestMatcher($app['request']),
        'form' => array('login_path' => '/login', 'check_path' => '/authenticate'),
        'logout' => array('logout_path' => '/logout'),
        'users' => $app->share(function () use ($app)
        {
            return new \Gitrepos\UserProvider($app);
        })
    )
);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => $app['conf']['db.driver'],
        'path' => $app['conf']['db.path']
    )
));

$app['model.factory'] = $app->share(function ($app)
{
    return new \Gitrepos\Models\ModelFactory($app);
});


$app->get(
    '/',
    function (\Silex\Application $app)
    {
        return 'List of repositories for user ' . $app['security']->getToken()->getUsername();
    });

$module = '\Gitrepos\Controllers\UserController';
$app->get('/login', $module . '::loginAction');

$app->match('/signin', $module . '::signinAction')->method('GET|POST');

$app->match(
    '/add',
    function (\Silex\Application $app)
    {
        if ($app['request']->getMethod() == 'POST')
        {
            return 'Create repository for user ' . $app['security']->getToken()->getUsername();
        }
        return 'Create repository form for user ' . $app['security']->getToken()->getUsername();
    }
)->method('GET|POST');


$app->get(
    '/{username}/{reponame}/',
    function (\Silex\Application $app, $username, $reponame)
    {
        return 'Details for  ' . $username . '/' . $reponame;
    });

$app->match(
    '/{username}/{reponame}/edit',
    function (\Silex\Application $app, $username, $reponame)
    {
        if ($app['request']->getMethod() == 'POST')
        {
            return 'Repository edition for  ' . $username . '/' . $reponame;
        }
        return 'Repository edition form for  ' . $username . '/' . $reponame;
    }
)->method('GET|POST');

$app->post(
    '/{username}/{reponame}/delete',
    function (\Silex\Application $app, $username, $reponame)
    {
        return 'Repository deletion of  ' . $username . '/' . $reponame;
    });

return $app;