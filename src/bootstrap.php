<?php
require_once __DIR__ . '/../vendor/autoload.php';

use
    \Symfony\Component\HttpFoundation\Request,
    \Symfony\Component\Validator\Constraints as Assert;

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

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver' => $app['conf']['db.driver'],
            'path' => $app['conf']['db.path']
        )
    )
);

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['model.factory'] = $app->share(
    function ($app) {
        return new \Gitrepos\Models\ModelFactory($app);
    }
);

return require __DIR__ . '/routes.php';