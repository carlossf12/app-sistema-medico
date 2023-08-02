<?php
// Require la configuración de conexión a la base de datos
require_once('../../backend/db/config.php');

// Verificar si se recibieron los IDs y correos electrónicos a eliminar
if (isset($_POST['ids']) && is_array($_POST['ids']) && count($_POST['ids']) > 0 && isset($_POST['emails']) && is_array($_POST['emails']) && count($_POST['emails']) > 0) {
    try {
        // Realizar la eliminación en la tabla 'patients'
        $sql_patients = "DELETE FROM patients WHERE id_patient IN (" . implode(',', array_fill(0, count($_POST['ids']), '?')) . ")";
        $stmt_patients = $conn->prepare($sql_patients);
        $stmt_patients->execute($_POST['ids']);

        // Obtener el número de filas afectadas en la tabla 'patients'
        $rowCount_patients = $stmt_patients->rowCount();

        // Realizar la eliminación en la tabla 'users'
        $sql_users = "DELETE FROM users WHERE email IN (" . implode(',', array_fill(0, count($_POST['emails']), '?')) . ")";
        $stmt_users = $conn->prepare($sql_users);
        $stmt_users->execute($_POST['emails']);

        // Obtener el número de filas afectadas en la tabla 'users'
        $rowCount_users = $stmt_users->rowCount();

        // Devolver una respuesta al frontend indicando que los registros se eliminaron exitosamente
        echo "Se eliminaron $rowCount_patients registros de pacientes y $rowCount_users registros de usuarios exitosamente.";
    } catch (PDOException $e) {
        // En caso de error en la consulta, mostrar el mensaje y detener el script
        die("Error al eliminar registros: " . $e->getMessage());
    }
} else {
    // Si no se recibieron los IDs o correos electrónicos, o no son válidos, devolver un mensaje de error al frontend
    echo "No se proporcionaron IDs o correos electrónicos válidos para eliminar registros.";
}
?>
