<?php
require_once __DIR__ . '/vendor/autoload.php';

group(
    'db',
    function () {
        desc('Dependency task for database operations');
        task(
            'init',
            function ($phake) {
                $app = new \Silex\Application();
                $Configuration = new \Gitrepos\Configuration(__DIR__ . '/conf/conf.json');
                if (!$env = getenv('APP_ENV')) {
                    $env = null;
                }
                $app['conf'] = $Configuration->get($env);
                $app->register(
                    new Silex\Provider\DoctrineServiceProvider(),
                    array(
                        'db.options' => array(
                            'driver' => $app['conf']['db.driver'],
                            'path' => $app['conf']['db.path']
                        )
                    )
                );
                $phake['app'] = $app;
            }
        );

        desc('Drops and recreates the database set in conf');
        task(
            'reset',
            'init',
            function ($phake) {
                $app = $phake['app'];
                $Database = new \Gitrepos\Database($app);
                $Database->reset();
            }
        );
    }
);

group(
    'tests',
    function () {
        desc('Run all tests');
        task(
            'all',
            function () {
                echo PHP_EOL . 'Running all tests' . PHP_EOL . PHP_EOL;
                passthru('phpunit');
            }
        );

        desc('Run unit tests');
        task(
            'unit',
            function () {
                echo PHP_EOL . 'Running unit tests' . PHP_EOL . PHP_EOL;
                passthru('phpunit -c tests/phpunit_unit_tests.xml.dist');
            }
        );

        desc('Run web tests');
        task(
            'web',
            function () {
                echo PHP_EOL . 'Running web test cases' . PHP_EOL . PHP_EOL;
                passthru('phpunit -c tests/phpunit_web_test_cases.xml.dist');
            }
        );
    }
);