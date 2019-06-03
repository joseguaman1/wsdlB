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
$dir = explode("json1.php/", $uri);
if (count($dir) >= 2) {
    $tokenDir = explode("/", $dir[1]);
    //por lo general el primero es la accion
    $accion = $tokenDir[0];
    if ($accion == 'listar_cursos') {
        $token = obtener_header("api-token");
        listar_cursos($token);
    }
    if ($accion == 'guardar_matricula') {
        $token = obtener_header("api-token");
        registro_matricula($token);
    }
    
    if ($accion == 'modificar_matricula') {
        if (isset($tokenDir[1]) && $tokenDir[1] != "") {
            $token = obtener_header("api-token");
            modificar_matricula($token, $tokenDir[1]);
        } else {
            echo json_encode(array("message" => "Recurso no encontrado"));
            http_response_code(404);
        }
    }
    
    if ($accion == 'obtener_estudiante') {
        if (isset($tokenDir[1]) && $tokenDir[1] != "") {
            $token = obtener_header("api-token");
            obtener_estudiante($token, $tokenDir[1]);
        } else {
            echo json_encode(array("message" => "Recurso no encontrado"));
            http_response_code(404);
        }
    }
    
} else {
    echo json_encode(array("message" => "Recurso no encontrado"));
    http_response_code(404);
}

function listar_cursos($token) {
    if ($token != "" && $token == 'EXAMEN_4_A') {
        $wsdl = new \contoller\Servicio();
        echo json_encode($wsdl->listar_cursos());
    } else {
        echo json_encode(array("mensaje" => "FALTA TOKEN",
            "codigo" => "401"));
    }
}

function obtener_estudiante($token, $cedula) {
    if ($token != "" && $token == 'EXAMEN_4_A') {
        $wsdl = new \contoller\Servicio();
        echo json_encode($wsdl->obtener_estudiante($cedula));
    } else {
        echo json_encode(array("mensaje" => "FALTA TOKEN",
            "codigo" => "401"));
    }
}

function registro_matricula($token) {
    if ($token != "" && $token == 'EXAMEN_4_A') {
        $json = file_get_contents('php://input');
        $datos = json_decode($json);
        $wsdl = new \contoller\Servicio();
        if (strlen(trim($datos->iso_pais)) > 0 
                && strlen(trim($datos->lenguaje)) > 0
                && strlen(trim($datos->curso)) > 0
                && strlen(trim($datos->paralelo)) > 0
                && strlen(trim($datos->monto)) > 0
                && strlen(trim($datos->repetidor)) > 0
                && strlen(trim($datos->identificador_estudiante)) > 0
                ) {
            $data = array();
            $data['pais'] = trim($datos->iso_pais);
            $data['lengu'] = trim($datos->lenguaje);
            $data['curso'] = trim($datos->curso);
            $data['para'] = trim($datos->paralelo);
            $data['monto'] = trim($datos->monto);
            $data['repe'] = (trim($datos->repetidor) == "si") ? true:false;
            $data['estu'] = trim($datos->identificador_estudiante);            
            $save = $wsdl->guardar($token, $data);
            if (count($save) > 0) {
                echo json_encode($save);
            } else {
                echo json_encode(array("message" => "No se pudo guardar",
                "codigo" => "500"));
            }
        } else {
            echo json_encode(array("message" => "FALTAN DATOS",
                "codigo" => "400"));
        }
    } else {
        echo json_encode(array("mensaje" => "FALTA TOKEN",
            "codigo" => "401"));
    }
}

function modificar_matricula($token, $external) {
    if ($token != "" && $token == 'EXAMEN_4_A') {
        $json = file_get_contents('php://input');
        $datos = json_decode($json);
        $wsdl = new \contoller\Servicio();
        if (strlen(trim($datos->iso_pais)) > 0 
                && strlen(trim($datos->lenguaje)) > 0
                && strlen(trim($datos->curso)) > 0
                && strlen(trim($datos->paralelo)) > 0
                && strlen(trim($datos->monto)) > 0
                && strlen(trim($datos->repetidor)) > 0
                && strlen(trim($datos->identificador_estudiante)) > 0
                ) {
            $data = array();
            $data['external'] = trim($external);
            $data['pais'] = trim($datos->iso_pais);
            $data['lengu'] = trim($datos->lenguaje);
            $data['curso'] = trim($datos->curso);
            $data['para'] = trim($datos->paralelo);
            $data['monto'] = trim($datos->monto);
            $data['repe'] = (trim($datos->repetidor) == "si") ? true:false;
            $data['estu'] = trim($datos->identificador_estudiante);            
            $save = $wsdl->modificar($token, $data);
            if (count($save) > 0) {
                echo json_encode($save);
            } else {
                echo json_encode(array("message" => "No se pudo guardar",
                "codigo" => "500"));
            }
        } else {
            echo json_encode(array("message" => "FALTAN DATOS",
                "codigo" => "400"));
        }
    } else {
        echo json_encode(array("mensaje" => "FALTA TOKEN",
            "codigo" => "401"));
    }
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
