<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Silex\Application();
$app['debug'] = true;

$app->get(
    '/{username}',
    function (\Silex\Application $app, $username)
    {
        return 'List of repositories for user ' . $username;
    });

$app->get(
    '/{username}/add',
    function (\Silex\Application $app, $username)
    {
        return 'Create repository form for user ' . $username;
    });

$app->post(
    '/{username}/create',
    function (\Silex\Application $app, $username)
    {
        return 'Create repository for user ' . $username;
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
    '/{username}/{reponame}/update',
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