<?php
require_once('../db/conexion.php');
$odjeto = new Conexion();
$conexion = $odjeto->Conectar();

// Obtener los datos enviados por la solicitud AJAX
$id_patient = $_POST['id_patient'];
$name = $_POST['name'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];
$data_update = date("Y-m-d H:i:s"); // Obtener la fecha actual del servidor

// Verificar y convertir la contraseña en caso de que no esté en formato MD5
if (strlen($password) != 32) {
    $password = md5($password);
}

// Preparar la consulta SQL para actualizar los datos en la tabla "patients"
$sql_update_patients = "UPDATE patients 
                        SET name = :name, first_name = :first_name, last_name = :last_name, 
                        phone = :phone, email = :email, data_update = :data_update
                        WHERE id_patient = :id_patient";

$resultado_update_patients = $conexion->prepare($sql_update_patients);
$resultado_update_patients->bindParam(':name', $name);
$resultado_update_patients->bindParam(':first_name', $first_name);
$resultado_update_patients->bindParam(':last_name', $last_name);
$resultado_update_patients->bindParam(':phone', $phone);
$resultado_update_patients->bindParam(':email', $email);
$resultado_update_patients->bindParam(':data_update', $data_update);
$resultado_update_patients->bindParam(':id_patient', $id_patient);
$resultado_update_patients->execute();

// Verificar si la actualización en la tabla 'patients' fue exitosa
if ($resultado_update_patients) {
    // Preparar la consulta SQL para actualizar el correo electrónico en la tabla "users"
    $sql_update_users = "UPDATE users 
                         SET name = :name, email = :email, password = :password, data_update = :data_update
                         WHERE email = :email";
    
    $resultado_update_users = $conexion->prepare($sql_update_users);
    $resultado_update_users->bindParam(':name', $name);
    $resultado_update_users->bindParam(':email', $email);
    $resultado_update_users->bindParam(':password', $password);
    $resultado_update_users->bindParam(':data_update', $data_update);
    $resultado_update_users->execute();
    
    // Verificar si la actualización en la tabla 'users' fue exitosa
    if ($resultado_update_users) {
        echo "Paciente actualizado exitosamente.";
    } else {
        echo "Error al actualizar datos en la tabla 'users'.";
    }
} else {
    // Si hubo un error en la actualización en la tabla 'patients', devolver un mensaje de error
    echo "Error al actualizar datos en la tabla 'patients'.";
}

$conexion = null;
?>
