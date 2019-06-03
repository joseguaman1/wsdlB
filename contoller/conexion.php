<?php
namespace contoller;
class Conexion {
    //Wizdler
    public function conexion() {
        $DBhost = "localhost";
        $DBuser = "sebastian";
        $DBpass = "sebastian";
        $DBname = "wsdlA";
        $DBcon = null;
        try {

            $DBcon = new \PDO("mysql:host=$DBhost;dbname=$DBname", $DBuser, $DBpass);
            $DBcon->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $ex) {

            die($ex->getMessage());
        }
        return $DBcon;
    }

}

?>