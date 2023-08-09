<?php
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    require("conexion2.php");

    $query = "SELECT * FROM paciente WHERE correo = '$correo'";
    $result_paciente = $mysqli->query($query);

    $query = "SELECT * FROM medico WHERE correo = '$correo'";
    $result_medico = $mysqli->query($query);

    $query = "SELECT * FROM administrador WHERE correo = '$correo'";
    $result_admin = $mysqli->query($query);

    if ($result_paciente->num_rows > 0) {
        $usuario = $result_paciente->fetch_assoc();
    } elseif ($result_medico->num_rows > 0) {
        $usuario = $result_medico->fetch_assoc();
    } elseif ($result_admin->num_rows > 0) {
        $usuario = $result_admin->fetch_assoc();
    } else {
        $usuario = false;
    }

    if ($usuario) {
        $nombre = $usuario["nombre"];
        $contrasena = $usuario["contrasena"];

        $subject = "Recuperación de Contraseña";
        $message = "Hola,\n\n";
        $message .= "Has solicitado recuperar tu contraseña.\n";
        $message .= "Del correo. $correo\n";
        $message .= "Tu contraseña es: $contrasena\n";
        $message .= "No contestar este correo.\n";
        $message .= "Si no solicitaste esto, por favor ignora este correo.\n";
        $message .= "\nGracias por Tu  preferencia.\n";

        // Envía el correo
        mail($correo, $subject, $message);

        $mensaje = "Se ha enviado un correo con la contraseña que estas utilizando.";

    } else {

        $mensaje = "El correo electrónico ingresado no corresponde a ningún usuario.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
        }

        p {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <form method="post" action="">
            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" required><br>
            <button type="submit">Consultar Contraseña</button>
        </form>
        <p><?php echo $mensaje;?></p>
    </div>
</body>
</html>

