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
//array("nombre" => $item["name"], "iso" => $item["alpha2Code"], "capital" => $item["capital"], "idioma" => $name["name"], "region" => $item["region"]);
$server->wsdl->addComplexType(
        'pais',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'nombre' => array('name' => 'nombre',
                'type' => 'xsd:string'), 
            'iso' => array('name' => 'iso',
                'type' => 'xsd:string'),
            'capital' => array('name' => 'capital',
                'type' => 'xsd:string'),
            'idioma' => array('name' => 'idioma',
                'type' => 'xsd:string'),            
            'region' => array('name' => 'region',
                'type' => 'xsd:string'))
);

$server->wsdl->addComplexType(
        'lenguaje',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'nombre' => array('name' => 'nombre',
                'type' => 'xsd:string'), 
            'iso' => array('name' => 'iso',
                'type' => 'xsd:string'))
);

$server->wsdl->addComplexType(
        'verificarMatricula',
        'complexType',
        'struct',
        'all',
        '',
        array('nro' => array('name' => 'nombre',
                'type' => 'xsd:integer'))
);

$server->wsdl->addComplexType(
        'matricula',
        'complexType',
        'struct',
        'all',
        '',
        array('estudiante' => array('name' => 'estudiante',
                'type' => 'xsd:string'),
            'pais' => array('name' => 'pais',
                'type' => 'xsd:string'),
            'lenguaje' => array('name' => 'lenguaje',
                'type' => 'xsd:string'),
            'curso' => array('name' => 'curso',
                'type' => 'xsd:string'),
            'paralelo' => array('name' => 'paralelo',
                'type' => 'xsd:string'),
            'external_id' => array('name' => 'external_id',
                'type' => 'xsd:string'),
            'monto' => array('name' => 'monto',
                'type' => 'xsd:string'),
            'repetidor' => array('name' => 'external_id',
                'type' => 'xsd:boolean'),
            'identificacion' => array('name' => 'identificacion',
                'type' => 'xsd:string'),
            'genero' => array('name' => 'genero',
                'type' => 'xsd:string'),
            'foto' => array('name' => 'foto',
                'type' => 'xsd:string'))
);

$server->wsdl->addComplexType('lista_paises', 'complexType', 'array', '',
        'SOAP-ENC:Array', array(),
        array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:pais[]')),
        'tns:pais');

$server->wsdl->addComplexType('lista_lenguaje', 'complexType', 'array', '',
        'SOAP-ENC:Array', array(),
        array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:lenguaje[]')),
        'tns:lenguaje');


$server->wsdl->addComplexType('lista_matricula', 'complexType', 'array', '',
        'SOAP-ENC:Array', array(),
        array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:matricula[]')),
        'tns:matricula');

//------------------ REGISTROS ---------//
$server->register('lista_paises',
        array(), // parameter
        array('return' => 'tns:lista_paises'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'Permite listar todos los departamentos');                   // description

$server->register('lista_lenguaje',
        array(), // parameter
        array('return' => 'tns:lista_lenguaje'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'Permite listar todos los proyectos');                   // description

$server->register('lista_matricula',
        array("token" => "xsd:string"), // parameter
        array('return' => 'tns:lista_matricula'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'Permite listar todas las matriculas');                   // description

$server->register('verificar_matricula',
        array("token" => "xsd:string", "identificacion" => "xsd:string"), // parameter
        array('return' => 'tns:verificarMatricula'), // output  array('return' => 'xsd:string'),     // output
        'urn:server', // namespace
        'urn:server#helloServer', // soapaction
        'rpc', // style
        'encoded', // use
        'Permite ver cuantas matriculas tiene el estudiante');                   // description


//----------------- FIN DE REGUSTROS -------//

function lista_matricula($token) {
    $wsdl = new \contoller\Servicio();
    if($token && $token == 'EXAMEN_4_A') {
        $datos = $wsdl->listar_matricula();
        return $datos;
    } else {
        return new soap_fault('500', '', 'Falta token', '');
    }
}
function verificar_matricula($token, $identificacion) {
    $wsdl = new \contoller\Servicio();
    if($token && $token == 'EXAMEN_4_A') {
        $datos = $wsdl->esta_matriculado($identificacion);
        return $datos;
    } else {
        return new soap_fault('500', '', 'Falta token', '');
    }
}
function lista_paises() {
    $wsdl = new \contoller\Servicio();
    $datos = $wsdl->listar_paises();
    return $datos;
}
function lista_lenguaje() {
    $wsdl = new \contoller\Servicio();
    $datos = $wsdl->listar_lenguajes();
    return $datos;
}


$server->service(file_get_contents("php://input"));