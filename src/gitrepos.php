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

$app->register(new FormServiceProvider(), array(
    'form.secret' => 'lk<qsfdq<s4d2q4sddf5y4(§4uè43(5§4(§35(4'
));

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback' => 'en',
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

$app->match(
    '/signin',
    function (\Silex\Application $app)
    {
        $form = $app['form.factory']->createBuilder('form')
            ->add('username')
            ->add('email')
            ->add('password', 'password')
            ->add('password2', 'password', array('label' => 'Retype password'))
            ->getForm();

        if ($app['request']->getMethod() == 'POST')
        {
            $form->bind($app['request']);

            if ($form->isValid())
            {
                $data = $form->getData();

                return print_r($data, true);
            }
        }

        return $app['twig']->render('signin.twig', array('form' => $form->createView()));
    }
)->method('GET|POST');

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