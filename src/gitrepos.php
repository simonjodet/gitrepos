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
        }),
    ),
);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => $app['conf']['db.driver'],
        'path' => $app['conf']['db.path']
    )
));

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
            ->add('username',
            'text',
            array(
                'constraints' => array(
                    new Assert\MinLength(3),
                    new Assert\MaxLength(64)
                )
            ))
            ->add('email',
            'text',
            array(
                'constraints' => array(
                    new Assert\Email()
                )
            ))
            ->add('password',
            'password',
            array(
                'always_empty' => false,
                'constraints' => array(
                    new Assert\MinLength(6),
                    new Assert\MaxLength(128)
                )
            ))
            ->add('password2',
            'password',
            array(
                'label' => 'Retype password',
                'always_empty' => false
            ))
            ->getForm();

        if ($app['request']->getMethod() == 'POST')
        {
            $form->bind($app['request']);
            $data = $form->getData();
            if ($data['password2'] != $data['password'])
            {
                $form->get('password2')->addError(new FormError('The two password fields don\'t match.'));
            }

            if ($form->isValid())
            {
                $UserModel = new \Gitrepos\UserModel($app);
                try
                {
                    $User = $UserModel->create(new \Gitrepos\User($data));
                    $app['security']->setToken(new UsernamePasswordToken($User, $User->getPassword(), 'user_firewall', array('ROLE_USER')));
                    return $app->redirect('/');
                }
                catch (\Gitrepos\Exceptions\DuplicateUsername $e)
                {
                    $form->get('username')->addError(new FormError('This username is already used.'));
                }
                catch (\Gitrepos\Exceptions\DuplicateEmail $e)
                {
                    $form->get('email')->addError(new FormError('This email address is already used.'));
                }
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