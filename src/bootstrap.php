<?php
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
            'host' => $app['conf']['db.host'],
            'dbname' => $app['conf']['db.dbname'],
            'user' => $app['conf']['db.user'],
            'password' => $app['conf']['db.password'],
            'charset' => $app['conf']['db.charset']
        )
    )
);

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['model.factory'] = $app->share(
    function ($app) {
        return new \Gitrepos\Models\ModelFactory($app);
    }
);
$app['passwords'] = $app->share(
    function () {
        return new \Gitrepos\Passwords();
    }
);

$app->error(
    function (\Exception $e) use ($app) {
        return 'Exception: "' . $e->getMessage() . '" in ' . $e->getFile() . ' at line ' . $e->getLine(
        ) . ':' . PHP_EOL . $e->getTraceAsString();
    }
);

if ($app['debug']) {
    $logger = new \Doctrine\DBAL\Logging\DebugStack();
    /**
     * @var $db \Doctrine\DBAL\Connection
     */
    $db = $app['db'];
    $db->getConfiguration()->setSQLLogger($logger);
    $app->finish(
        function () use ($app, $db) {
            $logger = $db->getConfiguration()->getSQLLogger();
            foreach ($logger->queries as $query) {
                $app['monolog']->addDebug(
                    $query['sql'],
                    array('params' => $query['params'], 'types' => $query['types'])
                );
            }
        }
    );
}

return require __DIR__ . '/routes.php';