<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use PDO;

class Artefacto
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function read(Request $request, Response $response, $args)
    {
        $sql = "SELECT * FROM artefacto";
        if (isset($args['id'])) {
            $sql .= " WHERE id = :id";
        }
        $db = $this->container->get('base_datos');
        $query = $db->prepare($sql);
        $query->execute(isset($args['id']) ? ['id' => $args['id']] : []);
        $res = $query->fetchAll();

        $response->getBody()->write(json_encode($res));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());

        $campos = "";
        $params = "";

        foreach ($body as $key => $value) {
            $campos .= $key . ", ";
            $params .= ":" . $key . ", ";
        }

        // Eliminar la última coma y espacio
        $campos = rtrim($campos, ", ");
        $params = rtrim($params, ", ");

        $sql = "INSERT INTO artefacto ($campos) VALUES ($params)";

        $con = $this->container->get('base_datos');
        $con->beginTransaction();

        try {
            $query = $con->prepare($sql);

            foreach ($body as $key => $value) {
                $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR;
                $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                $query->bindValue(":$key", $value, $TIPO);
            }

            $query->execute();
            $con->commit();

            $status = 201;
        } catch (\PDOException $e) {
            $con->rollBack();
            $status = $e->getCode() == 23000 ? 409 : 500;
        }

        // Limpiar recursos
        $query = null;
        $con = null;

        return $response->withStatus($status);
    }

    public function update(Request $request, Response $response, $args) //!Actualizo un artefacto
    {
        $body = json_decode($request->getBody());

        $id = $args['id'];
        if (isset($body->id)) {
            unset($body->id); //?Eliminar el id del body
        }

        if (isset($body->codigo_artefacto)) {
            unset($body->codigo_artefacto); //?Eliminar el codigo_artefacto del body
        }

        $sql = "UPDATE artefacto SET ";
        foreach ($body as $key => $value) {
            $sql .= "$key = :$key, ";
        }
        $sql = substr($sql, 0, -2); //Eliminar la última coma y espacio
        $sql .= " WHERE id = :id;"; //?Agregar la condición para el id

        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        foreach ($body as $key => $value) {
            $TIPO = gettype($value) == "integer" ? PDO::PARAM_INT : PDO::PARAM_STR; //?Definir el tipo de dato
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS); // Sanitizar el valor
            $query->bindValue($key, $value, $TIPO);
        }

        $query->bindValue("id", $id, PDO::PARAM_INT); //!Agregar el id a la consulta porque lo eliminamos del body

        $query->execute();

        $status = $query->rowCount() > 0 ? 201 : 204; //?201 Creado, 204 Sin contenido

        $query = null; //Cerrar la consulta
        $con = null; //Cerrar la conexión

        return $response->withStatus($status);
    }


    public function delete(Request $request, Response $response, $args)
    {
        $sql = "DELETE FROM artefacto WHERE id = :id";
        $db = $this->container->get('base_datos');
        $query = $db->prepare($sql);
        $query->execute(['id' => $args['id']]);

        return $response->withStatus(204);
    }

    public function filtrar(Request $request, Response $response, $args) //!Filtrar artefactos
    {
        // Obtener los parámetros de la consulta
        $datos = $request->getQueryParams();

        // Construir la consulta SQL
        $sql = "SELECT * FROM artefacto WHERE ";

        // Añadir condiciones para cada uno de los filtros proporcionados
        foreach ($datos as $key => $value) {
            $sql .= "$key LIKE :$key AND ";
        }

        // Eliminar la última coma y espacio del "AND"
        $sql = rtrim($sql, 'AND ') . ";";

        // Preparar la consulta en la base de datos
        $con = $this->container->get('base_datos');
        $query = $con->prepare($sql);

        // Vincular los parámetros de la consulta
        foreach ($datos as $key => $value) {
            $query->bindValue(":$key", "%$value%", PDO::PARAM_STR); // Usamos LIKE con comodín
        }

        // Ejecutar la consulta
        $query->execute();

        // Obtener los resultados
        $res = $query->fetchAll();

        // Determinar el código de estado basado en los resultados
        $status = $query->rowCount() > 0 ? 200 : 204;

        // Limpiar recursos
        $query = null;
        $con = null;

        // Escribir los resultados en la respuesta
        $response->getBody()->write(json_encode($res));

        // Retornar la respuesta con los datos y el código de estado
        return $response
            ->withHeader('Content-Type', 'application/json') // Asegúrate de especificar que la respuesta es JSON
            ->withStatus($status);
    }
}
