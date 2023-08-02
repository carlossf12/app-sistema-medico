<?php
require_once('../db/conexion.php');
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT s.id_specialty,
                    s.name,
                    s.description
            FROM specialty s";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
$jsonData = json_encode(array("data" => $data), JSON_UNESCAPED_UNICODE);
print $jsonData;
$conexion=null;

// Guardar el JSON en un archivo
$archivo = '../json/specialties.json';
file_put_contents($archivo, $jsonData);
?>
