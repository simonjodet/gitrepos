<?php
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Validator\Constraints as Assert;

//function errorResponse($code, $message, $doc)
//{
//    if (!array_key_exists($code, \Symfony\Component\HttpFoundation\Response::$statusTexts)) {
//        $code = 500;
//        $message = 'Internal server error';
//        $doc = '';
//    }
//    $error_body =
//        array(
//            'code' => 401,
//            'message' => 'Bad credentials',
//            'doc' => '/docs/sessions.json'
//        );
//    $response = $app->json($error_body, 401);
//}

$app->before(
    function (Request $request) use ($app) {
        $app['request.body_params'] = json_decode($request->getContent(), true);
    }
);
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['users.controller'] = $app->share(
    function () use ($app) {
        return new \Gitrepos\Controllers\UsersController($app, $app['request']);
    }
);
$app->post('/v1/users', 'users.controller:postAction');

$app['sessions.controller'] = $app->share(
    function () use ($app) {
        return new \Gitrepos\Controllers\SessionsController($app, $app['request']);
    }
);
$app->post('/v1/sessions', 'sessions.controller:postAction');
$app->delete('/v1/sessions/current', 'sessions.controller:deleteCurrentAction');

$app['keys.controller'] = $app->share(
    function () use ($app) {
        return new \Gitrepos\Controllers\KeysController($app, $app['request']);
    }
);
$app->post('/v1/keys', 'keys.controller:postAction');


return $app;