<?php

//root
$app->get(
    '/',
    function (\Silex\Application $app) {
        return 'List of repositories for user ' . $app['security']->getToken()->getUsername();
    }
);

//user-related routes
$module = '\Gitrepos\Controllers\UserController';
$app->get('/login', $module . '::loginAction');

$app->match('/signin', $module . '::signinAction')->method('GET|POST');

$app->match(
    '/add',
    function (\Silex\Application $app) {
        if ($app['request']->getMethod() == 'POST') {
            return 'Create repository for user ' . $app['security']->getToken()->getUsername();
        }
        return 'Create repository form for user ' . $app['security']->getToken()->getUsername();
    }
)->method('GET|POST');

//repository-related routes
$app->get(
    '/{username}/{reponame}/',
    function (\Silex\Application $app, $username, $reponame) {
        return 'Details for  ' . $username . '/' . $reponame;
    }
);

$app->match(
    '/{username}/{reponame}/edit',
    function (\Silex\Application $app, $username, $reponame) {
        if ($app['request']->getMethod() == 'POST') {
            return 'Repository edition for  ' . $username . '/' . $reponame;
        }
        return 'Repository edition form for  ' . $username . '/' . $reponame;
    }
)->method('GET|POST');

$app->post(
    '/{username}/{reponame}/delete',
    function (\Silex\Application $app, $username, $reponame) {
        return 'Repository deletion of  ' . $username . '/' . $reponame;
    }
);

return $app;