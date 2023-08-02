<?php
require_once('../db/conexion.php');
$odjeto = new Conexion();
$conexion = $odjeto->Conectar();

// Obtener los datos enviados por la solicitud AJAX
$name = $_POST['name'];
$first_name = $_POST['first_name']; // Corrige el nombre de la variable aquí
$last_name = $_POST['last_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = md5($_POST['password']);
$status = 1;
$rol = 3;
$data_create = date("Y-m-d H:i:s"); // Se puede obtener la fecha actual del servidor

// Verificar si el correo electrónico ya existe en la tabla 'users'
$sql_check_email = "SELECT COUNT(*) AS email_count FROM users WHERE email = '$email'";
$result_check_email = $conexion->query($sql_check_email);
$row_check_email = $result_check_email->fetch(PDO::FETCH_ASSOC);

if ($row_check_email['email_count'] > 0) {
    // Si el correo electrónico ya existe, mostrar un mensaje de error y detener la inserción
    echo "El correo electrónico ya existe en la base de datos. No se pudo agregar el paciente.";
} else {
    // Preparar la consulta SQL para insertar los datos en la tabla "patients"
    $sql = "INSERT INTO patients (name, first_name, last_name, phone, email, status, data_create) 
            VALUES ('$name', '$first_name', '$last_name', '$phone', '$email', '$status', '$data_create')";
    
    $resultado = $conexion->prepare($sql);
    $resultado->execute();
    
    // Verificar si la inserción en la tabla 'patients' fue exitosa
    if ($resultado) {
        // Preparar la consulta SQL para insertar el correo electrónico en la tabla "users"
        $sql_users = "INSERT INTO users (name, email, password, rol, data_create) VALUES ('$name', '$email', '$password', '$rol', '$data_create')"; // Ajusta las columnas según tu tabla 'users'
        
        $resultado_users = $conexion->prepare($sql_users);
        $resultado_users->execute();
        
        // Verificar si la inserción en la tabla 'users' fue exitosa
        if ($resultado_users) {
            echo $name . " agregado.";
        } else {
            echo $name . " Error al guardar.";
        }
    } else {
        // Si hubo un error en la inserción en la tabla 'patients', devolver un mensaje de error
        echo "Error al guardar paciente.";
    }
}

$conexion = null;
?>
