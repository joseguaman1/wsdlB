<?php

namespace contoller;

require_once 'conexion.php';

class Servicio {

    protected $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    private function leerDataPaises() {
        $data = file_get_contents("./datos/country.json");
        return $data;
    }

    private function convertirArreglo($json) {

        $products = json_decode($json, true);
        return $products;
    }

    function listar_paises() {
        $paises = $this->leerDataPaises();
        $products = $this->convertirArreglo($paises);
        $pais = array();
        foreach ($products as $item) {
            $lenguaje = $item["languages"];
            $name = $lenguaje[0];
            $pais[] = array("nombre" => $item["name"], "iso" => $item["alpha2Code"], "capital" => $item["capital"], "idioma" => $name["name"], "region" => $item["region"]);
        }
        return $pais;
    }

    function buscar_pais_codigo($codigo) {
        $paises = $this->leerDataPaises();
        $products = $this->convertirArreglo($paises);
        $pais = array();
        foreach ($products as $item) {
            if ($item["alpha2Code"] == $codigo) {
                $lenguaje = $item["languages"];
                $name = $lenguaje[0];
                $pais[] = array("nombre" => $item["name"], "iso" => $item["alpha2Code"], "capital" => $item["capital"], "idioma" => $name["name"], "region" => $item["region"]);
                break;
            }
        }
        return $pais;
    }

    /*function buscar_pais_espanol() {
        $paises = $this->leerDataPaises();
        $products = $this->convertirArreglo($paises);
        $pais = array();
        foreach ($products as $product) {
            $lenguaje = $product["languages"];
            $name = $lenguaje[0];
            if ($name["iso639_1"] == "es") {
                $pais[] = $product;
            }
        }
        echo json_encode($pais);
    }*/

    function listar_lenguajes() {
        $paises = $this->leerDataPaises();
        $products = $this->convertirArreglo($paises);
        $pais = array();
        foreach ($products as $product) {
            $lenguaje = $product["languages"];
            $name = $lenguaje[0];
            $band = $this->verificar_arreglo($name["iso639_1"], $pais);
            if ($band) {
                $pais[] = array("nombre" => $name["name"], "iso" => $name["iso639_1"]);
            }
        }
        return $pais;
    }

    private function verificar_arreglo($lenguaje, $arreglo) {
        $band = true;
        foreach ($arreglo as $item) {
            if ($item["iso"] == $lenguaje) {
                $band = false;
                break;
            }
        }
        return $band;
    }
    
    function listar_matricula() {
        $obj = $this->conexion->conexion();
        $query = "select * from matricula inner join estudiante on estudiante.id = matricula.id_persona";
        $stmt = $obj->prepare($query);
        $stmt->execute();
        $datos = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row["foto"] = 'fotos/'.$row["foto"];
            $row["estudiante"] = $row["apellidos"].' '.$row["nombres"];
            $datos[] = $row;
        }
        //echo json_encode($datos);
        return $datos;
    }
    
    function esta_matriculado($cedula) {
        $obj = $this->conexion->conexion();
        $query = "select * from matricula inner join estudiante on estudiante.id = matricula.id_persona where identificacion = :ced";
        $stmt = $obj->prepare($query);
        $stmt->bindParam("ced", $cedula, \PDO::PARAM_INT);
        $stmt->execute();        
        $aux = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {            
            $aux[] = $row;
        }
        $datos = array("nro"=> count($aux));
        //echo json_encode($datos);
        return $datos;
    }
    
    function obtener_estudiante($cedula) {
        $obj = $this->conexion->conexion();
        $query = "select * from estudiante where identificacion = :cedula";
        $stmt = $obj->prepare($query);
        $stmt->bindParam("cedula", $cedula, \PDO::PARAM_INT);
        $stmt->execute();
        $datos = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $data = array(
                "identificador" => $row["id"],
                "identificacion" => $row["identificacion"],
                "genero" => $row["genero"],
                "direccion" => $row["direccion"],
                "esta_habilitado" => $row["habilitado"],
                "foto" => 'fotos/'.$row["foto"],                
                "estudiante" => $row["apellidos"].' '.$row["nombres"]);
            $datos = $data;
        }
       // echo json_encode($datos);
        return $datos;
    }
    function listar_cursos() {
        $arreglo = array();
        $arreglo[] = array("nombre"=>"primero", "paralelo"=>"A,B,C,D");
        $arreglo[] = array("nombre"=>"segundo", "paralelo"=>"A,B,C");
        $arreglo[] = array("nombre"=>"tercero", "paralelo"=>"A,B,C");
        $arreglo[] = array("nombre"=>"cuarto", "paralelo"=>"A,B");
        $arreglo[] = array("nombre"=>"quinto", "paralelo"=>"A,B");
        $arreglo[] = array("nombre"=>"sexto", "paralelo"=>"A");
       // echo json_encode($arreglo);
        return $arreglo;
    }
    private function gen_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    

    function guardar($token, $data) {
        if ($token != "" && $token != "EXAMEN_4_A") {
            return array("message" => "No puede acceder a este recurso",
                "codigo" => "401");
        } else {
            $obj = $this->conexion->conexion();
            $external = $this->gen_uuid();
            $query = "INSERT INTO matricula (external_id, pais, lenguaje, "
                    . "curso, paralelo, id_persona, "
                    . "monto, repetidor) "
                    . "values(:external, :pais, :lengu, :curso, :para, :estu, :monto, :repe "
                    . ")";
            $stmt = $obj->prepare($query);
            $stmt->bindParam("external", $external, \PDO::PARAM_STR);
            $stmt->bindParam("pais", $data['pais'], \PDO::PARAM_STR);
            $stmt->bindParam("lengu", $data['lengu'], \PDO::PARAM_STR);
            $stmt->bindParam("curso", $data['curso'], \PDO::PARAM_STR);
            $stmt->bindParam("para", $data['para'], \PDO::PARAM_STR);
            $stmt->bindParam("monto", $data['monto'], \PDO::PARAM_STR);
            $stmt->bindParam("repe", $data['repe'], \PDO::PARAM_BOOL);
            $stmt->bindParam("estu", $data['estu'], \PDO::PARAM_INT);            
            $stmt->execute();
            $newId = $obj->lastInsertId();
            if ($newId > 0) {
                return array("mensaje" => "Se ha guardado correctamente",
                    "codigo" => "200"
                );
            } else {
                return array();
            }
        }
    }

    function modificar($token, $data) {
        if ($token != "" && $token != "EXAMEN_4_A") {
            return array("message" => "No puede acceder a este recurso",
                "codigo" => "401");
        } else {
            $obj = $this->conexion->conexion();

            $query = "UPDATE matricula set pais = :pais, lenguaje = :lengu, "
                    . "curso = :curso, paralelo = :para, "
                    . "monto = :monto, repetidor = :repe, "
                    . "id_persona = :estu, "
                    . "monto = :monto "
                    . " where external_id = :external";
            
            $stmt = $obj->prepare($query);
            $stmt->bindParam("pais", $data['pais'], \PDO::PARAM_STR);
            $stmt->bindParam("lengu", $data['lengu'], \PDO::PARAM_STR);
            $stmt->bindParam("curso", $data['curso'], \PDO::PARAM_STR);
            $stmt->bindParam("para", $data['para'], \PDO::PARAM_STR);
            $stmt->bindParam("monto", $data['monto'], \PDO::PARAM_STR);
            $stmt->bindParam("repe", $data['repe'], \PDO::PARAM_BOOL);
            $stmt->bindParam("estu", $data['estu'], \PDO::PARAM_INT);            
            $stmt->bindParam("external", $data['external'], \PDO::PARAM_STR);            
            if ($stmt->execute()) {
                return array("mensaje" => "Se ha modificado",
                    "codigo" => "200"
                );
            } else {
                return array();
            }
        }
    }

}
