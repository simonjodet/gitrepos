<?php
require_once __DIR__ . '/../vendor/autoload.php';

use
    \Symfony\Component\HttpFoundation\Request,
    \Symfony\Component\Validator\Constraints as Assert,
    \Silex\Provider\FormServiceProvider;

$app = new \Silex\Application();
$Configuration = new \Gitrepos\Configuration(__DIR__ . '/../conf/conf.json');
if (!$env = getenv('APP_ENV')) {
    $env = null;
}
$app['conf'] = $Configuration->get($env);
$app['debug'] = $app['conf']['app.debug'];

date_default_timezone_set($app['conf']['timezone']);

$app->register(
    new Silex\Provider\MonologServiceProvider(),
    array(
        'monolog.name' => 'GR',
        'monolog.logfile' => $app['conf']['monolog.logfile'],
        'monolog.level' => constant('\Monolog\Logger::' . $app['conf']['monolog.level'])
    )
);

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => __DIR__ . '/views'
    )
);

$app->register(
    new FormServiceProvider(),
    array(
        'form.secret' => $app['conf']['form.secret']
    )
);

$app->register(
    new Silex\Provider\TranslationServiceProvider(),
    array(
        'locale_fallback' => $app['conf']['translation.locale_fallback']
    )
);

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new \Silex\Provider\SecurityServiceProvider());
$app['security.firewalls'] = array(
    'user_firewall' => array(
        'pattern' => new \Gitrepos\UserRequestMatcher($app['request']),
        'form' => array('login_path' => '/login', 'check_path' => '/authenticate'),
        'logout' => array('logout_path' => '/logout'),
        'users' => $app->share(
            function () use ($app) {
                return new \Gitrepos\UserProvider($app);
            }
        )
    )
);

$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver' => $app['conf']['db.driver'],
            'path' => $app['conf']['db.path']
        )
    )
);

$app['model.factory'] = $app->share(
    function ($app) {
        return new \Gitrepos\Models\ModelFactory($app);
    }
);

return require __DIR__ . '/routes.php';