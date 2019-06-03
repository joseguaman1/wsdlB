<?php

require_once './contoller/Servicio.php';
require_once './contoller/conexion.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Content-Type, api-token');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-type: application/json');
header("HTTP/1.1 200");
$uri = $_SERVER['REQUEST_URI'];
$dir = explode("json2.php/", $uri);
if (count($dir) >= 2) {
    $tokenDir = explode("/", $dir[1]);
    //por lo general el primero es la accion
    $accion = $tokenDir[0];
    if ($accion == 'listar_matricula') {
        $token = obtener_header("api-token");
        listar_matricula($token);
    }
    if ($accion == 'verificar_matricula') {
        if (isset($tokenDir[1]) && $tokenDir[1] != "") {
            $token = obtener_header("api-token");
            verificar_matricula($token, $tokenDir[1]);
        } else {
            echo json_encode(array("message" => "Recurso no encontrado"));
            http_response_code(404);
        }
    }
    
    if ($accion == 'listar_paises') {        
        listar_paises();
    }
    if ($accion == 'listar_lenguajes') {        
        listar_lenguajes();
    }
    
} else {
    echo json_encode(array("message" => "Recurso no encontrado"));
    http_response_code(404);
}

function listar_matricula($token) {
    if ($token != "" && $token == 'EXAMEN_4_A') {
        $wsdl = new \contoller\Servicio();
        echo json_encode($wsdl->listar_matricula());
    } else {
        echo json_encode(array("mensaje" => "FALTA TOKEN",
            "codigo" => "401"));
    }
}

function verificar_matricula($token, $identificacion) {
    if ($token != "" && $token == 'EXAMEN_4_A') {
        $wsdl = new \contoller\Servicio();
        echo json_encode($wsdl->esta_matriculado($identificacion));
    } else {
        echo json_encode(array("mensaje" => "FALTA TOKEN",
            "codigo" => "401"));
    }
}

function listar_paises() {
    $wsdl = new \contoller\Servicio();
        echo json_encode($wsdl->listar_paises());
}

function listar_lenguajes() {
    $wsdl = new \contoller\Servicio();
        echo json_encode($wsdl->listar_lenguajes());
}




function obtener_header($nombreToken) {
    $valorToken = "";
    foreach (getallheaders() as $nombre => $valor) {

        if ($nombre == $nombreToken)
            $valorToken = $valor;
    }
    $headers = apache_request_headers();
    foreach ($headers as $nombre => $valor) {
        if ($nombre == $nombreToken)
            $valorToken = $valor;
//        echo "$nombre: $valor\n";
    }
    return $valorToken;
}
