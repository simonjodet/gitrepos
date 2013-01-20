<?php
use Symfony\Component\HttpFoundation\Request;

$app->post(
    '/v1/users',
    function (Request $request) use ($app) {
        $response = $app->json(null, 201);
        $response->setContent('');
        return $response;
    }
);

return $app;