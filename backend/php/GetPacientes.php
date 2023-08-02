<?php
require_once('../db/conexion.php');
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT p.id_patient,
                    p.name,
                    p.first_name,
                    p.last_name,
                    p.phone,
                    p.email,
                    u.password,
                    u.rol
            FROM patients p
            inner join users u on p.email = u.email
            WHERE u.rol = 3";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
$jsonData = json_encode(array("data" => $data), JSON_UNESCAPED_UNICODE);
print $jsonData;
$conexion=null;

// Guardar el JSON en un archivo
$archivo = '../json/patients.json';
file_put_contents($archivo, $jsonData);
?>
