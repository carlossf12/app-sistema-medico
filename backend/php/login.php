<?php
require_once('../db/config.php');

// Iniciar la sesión
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se han recibido los campos de email y password
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $errorMessages = array();
        $responseData = array();

        // Get data from FORM
        $email = $_POST['email'];
        $password = MD5($_POST['password']);

        if (empty($errorMessages)) {
            try {
                $stmt = $conn->prepare('SELECT email, password, rol FROM users WHERE email = :email');

                $stmt->execute(array(
                    ':email' => $email
                ));
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($data === false) {
                    $errorMessages[] = "El correo $email no encontrado.";
                } else {
                    // Verificar la contraseña
                    if ($password == $data['password']) {
                        // Inicio de sesión exitoso
                        $_SESSION['email'] = $data['email'];
                        $_SESSION['rol'] = $data['rol'];

                        // Redireccionar según el rol
                       $errorMessages[] = $data['rol'];
                    } else {
                        // Contraseña incorrecta el 1 es para que se muestre el error en el frontend en el notify en el if
                        $errorMessages[] = 0;
                    }
                }
            } catch (PDOException $e) {
                $errorMessages[] = 'Error en la base de datos: ' . $e->getMessage();
            }
        }
    } else {
        // Si los campos no están presentes en la petición
        $errorMessages[] = "Error: Campos de email y/o password no encontrados en la petición.";
    }

    // Aquí puedes mostrar los errores al usuario si es necesario
    if (!empty($errorMessages)) {
        foreach ($errorMessages as $errorMessage) {
            echo $errorMessage;
        }
    }
} else {
    // Si la petición no es de tipo POST
    echo "Error: Petición HTTP inesperada.";
}
?>
