<?php
namespace App;

use App\controllers\Producto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

// Agrupar las rutas relacionadas con 'producto'
$app->group('/api', function (RouteCollectorProxy $api) {
    $api->group('/producto', function(RouteCollectorProxy $producto) {
        // Usar el controlador ProductoController para las rutas
        $producto->get('/read[/{id}]', Producto::class . ':read');  // Leer producto(s)
        $producto->post('/create', Producto::class . ':create');   // Crear producto
    });
});

$app->get('/producto', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Accediendo a producto!");
    return $response;
});

$app->get('/producto/read', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Accediendo a producto!");
    return $response;
});
