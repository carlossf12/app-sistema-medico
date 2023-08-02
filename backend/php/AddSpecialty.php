<?php
require_once('../db/conexion.php');
$odjeto = new Conexion();
$conexion = $odjeto->Conectar();

// Obtener los datos enviados por la solicitud AJAX
$name = $_POST['name'];
$description = $_POST['description'];
$data_create = date("Y-m-d H:i:s"); // Se puede obtener la fecha actual del servidor

// Preparar la consulta SQL para insertar los datos en la tabla "specialty"
$sql = "INSERT INTO specialty (name, description, data_create) VALUES ('$name', '$description', '$data_create')";

$resultado = $conexion->prepare($sql);
$resultado->execute();

// Verificar si la inserción fue exitosa
if ($resultado) {
    echo $name . " agregado.";
} else {
    // Si hubo un error en la inserción, devolver un mensaje de error
    echo "Error al guardar Area Medica.";
}

$conexion = null;
?>
