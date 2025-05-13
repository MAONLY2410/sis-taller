<?php
namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use PDO;

class Producto
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function read(Request $request, Response $response, $args)
    {
        $sql = "SELECT * FROM productos";

        if (isset($args["id"])) {
            $sql .= " WHERE id = :id";
        }
        $sql .= " LIMIT 0, 5";
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);
        if(isset($args['id'])){
            $query->execute(["id" => $args['id']]);
        } else {
            $query->execute();
        }

        $res = $query->fetchAll();

        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;

        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);

    }

    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);

    }
}