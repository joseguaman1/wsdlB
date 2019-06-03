<?php

require_once './contoller/Servicio.php';
require_once './contoller/conexion.php';
require_once './nusoap/lib/nusoap.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-type: application/xml');

$server = new \soap_server();
$server->configureWSDL('server', 'urn:server');
$server->wsdl->schemaTargetNamespace = 'urn:server';

$server->wsdl->addComplexType(
        'mensaje',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'mensaje' => array('name' => 'mensaje',
                'type' => 'xsd:string'), 
            'codigo' => array('name' => 'codigo',
                'type' => 'xsd:string'))
);

/*$server->wsdl->addComplexType(
        'mensaje',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'mensaje' => array('name' => 'mensaje',
                'type' => 'xsd:string'), 
            'codigo' => array('name' => 'codigo',
                'type' => 'xsd:string'))
);*/

$server->wsdl->addComplexType(
        'estudiante',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'identificador' => array('name' => 'identificador',
                'type' => 'xsd:string'), 
            'identificacion' => array('name' => 'identificacion',
                'type' => 'xsd:string'),
            'genero' => array('name' => 'genero',
                'type' => 'xsd:string'),
            'direccion' => array('name' => 'direccion',
                'type' => 'xsd:string'),
            'esta_habilitado' => array('name' => 'esta_habilitado',
                'type' => 'xsd:string'),
            'foto' => array('name' => 'foto',
                'type' => 'xsd:string'),
            'estudiante' => array('name' => 'estudiante',
                'type' => 'xsd:string'))
);

$server->wsdl->addComplexType(
        'curso',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'nombre' => array('name' => 'nombre',
                'type' => 'xsd:string'), 
            'paralelo' => array('name' => 'paralelo',
                'type' => 'xsd:string'))
);

$server->wsdl->addComplexType('lista_curso', 'complexType', 'array', '',
        'SOAP-ENC:Array', array(),
        array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:curso[]')),
        'tns:curso');
//------------------ REGISTROS ---------//

$server->register('listar_curso',
        array("token" => "xsd:string"), // parameter
        array('return' => 'tns:lista_curso'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'listado de cursos');                   // description

$server->register('obtener_estudiante',
        array("token" => "xsd:string", "identificacion"=>"xsd:string"), // parameter
        array('return' => 'tns:estudiante'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'obtiene el estudiante por identificacion');                   // description

$server->register('guardar_matricula',
        array("token" => "xsd:string",
            "iso_pais" => "xsd:string",
            "lenguaje" => "xsd:string",
            "curso" => "xsd:string",
            "paralelo" => "xsd:integer",
            "monto" => "xsd:decimal",
            "repetidor" => "xsd:string",
            "identificador_estudiante" => "xsd:integer"
            ), // parameter
        array('return' => 'tns:mensaje'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'Guardar matricula');                   // description

$server->register('modificar_matricula',
        array("token" => "xsd:string",
            "external" => "xsd:string",
            "iso_pais" => "xsd:string",
            "lenguaje" => "xsd:string",
            "curso" => "xsd:string",
            "paralelo" => "xsd:integer",
            "monto" => "xsd:decimal",
            "repetidor" => "xsd:string",
            "identificador_estudiante" => "xsd:integer"), // parameter
        array('return' => 'tns:mensaje'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#obtener_departamento', // soapaction
        'rpc', // style
        'encoded', // use
        'Permite modificar una matricula');                   // description
//----------------- FIN DE REGUSTROS -------//

function guardar_matricula($token, $iso_pais, $lenguaje, $curso, $paralelo, $monto, 
        $repetidor, $identificador_estudiante) {
    $wsdl = new \contoller\Servicio();
    if($token && $token == 'EXAMEN_4_A') {
        if (strlen(trim($iso_pais)) > 0 && strlen(trim($lenguaje)) > 0
                && strlen(trim($curso)) > 0
                && strlen(trim($paralelo)) > 0
                && strlen(trim($monto)) > 0
                && strlen(trim($repetidor)) > 0
                && strlen(trim($identificador_estudiante)) > 0
                ) {
            $data = array();
            $data['pais'] = trim($iso_pais);
            $data['lengu'] = trim($lenguaje);
            $data['curso'] = trim($curso);
            $data['para'] = trim($paralelo);
            $data['monto'] = trim($monto);
            $data['repe'] = (trim($repetidor) == "si") ? true:false;
            $data['estu'] = trim($identificador_estudiante);            
            $save = $wsdl->guardar($token, $data);
            if (count($save) > 0) {
                return $save;
            } else {
                return new soap_fault('500', '', "No se pudo guardar", '');
            }
        } else {
            return new soap_fault('500', '', 'Faltan datos', '');
        }
        $datos = $wsdl->obtener_departamentos();
        return $datos;
    } else {
        return new soap_fault('500', '', 'Falta token', '');
    }
}

function modificar_matricula($token, $external, $iso_pais, $lenguaje, $curso, $paralelo, $monto, 
        $repetidor, $identificador_estudiante) {
    $wsdl = new \contoller\Servicio();
    if($token && $token == 'EXAMEN_4_A') {
        if (strlen(trim($iso_pais)) > 0 && strlen(trim($lenguaje)) > 0
                && strlen(trim($curso)) > 0
                && strlen(trim($paralelo)) > 0
                && strlen(trim($monto)) > 0
                && strlen(trim($repetidor)) > 0
                && strlen(trim($identificador_estudiante)) > 0
                ) {
            $data = array();
            $data['external'] = trim($external);
            $data['pais'] = trim($iso_pais);
            $data['lengu'] = trim($lenguaje);
            $data['curso'] = trim($curso);
            $data['para'] = trim($paralelo);
            $data['monto'] = trim($monto);
            $data['repe'] = (trim($repetidor) == "si") ? true:false;
            $data['estu'] = trim($identificador_estudiante);            
            $save = $wsdl->modificar($token, $data);
            if (count($save) > 0) {
                return $save;
            } else {
                return new soap_fault('500', '', "No se pudo modificar", '');
            }
        } else {
            return new soap_fault('500', '', 'Faltan datos', '');
        }
        $datos = $wsdl->obtener_departamentos();
        return $datos;
    } else {
        return new soap_fault('500', '', 'Falta token', '');
    }
}
function listar_curso($token) {
    if($token && $token == 'EXAMEN_4_A') {
        $wsdl = new \contoller\Servicio();
        return $wsdl->listar_cursos();
    } else {
        return new soap_fault('500', '', 'Falta token', '');
    }
}
function obtener_estudiante($token, $identificacion) {
    if($token && $token == 'EXAMEN_4_A') {
        $wsdl = new \contoller\Servicio();
        return $wsdl->obtener_estudiante($identificacion);
    } else {
        return new soap_fault('500', '', 'Falta token', '');
    }
}
$server->service(file_get_contents("php://input"));