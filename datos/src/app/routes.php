<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->group('/artefacto', function (RouteCollectorProxy $artefacto) {
        $artefacto->get('/read/{id}', Artefacto::class . ':read'); // Ruta corregida
        $artefacto->post('', Artefacto::class . ':create');
        $artefacto->put('/{id}', Artefacto::class . ':update');
        $artefacto->delete('/{id}', Artefacto::class . ':delete');
        $artefacto->get('/filtrar', Artefacto::class . ':filtrar');
    });
});
