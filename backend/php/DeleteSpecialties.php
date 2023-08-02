<?php
// Require la configuración de conexión a la base de datos
require_once('../../backend/db/config.php');

// Verificar si se recibieron los IDs a eliminar
if (isset($_POST['ids']) && is_array($_POST['ids']) && count($_POST['ids']) > 0) {
    try {
        // Preparar la consulta SQL de eliminación usando parámetros para evitar inyección de SQL
        $sql = "DELETE FROM specialty WHERE id_specialty IN (" . implode(',', array_fill(0, count($_POST['ids']), '?')) . ")";
        $stmt = $conn->prepare($sql);

        // Ejecutar la consulta con los IDs recibidos
        $stmt->execute($_POST['ids']);

        // Obtener el número de filas afectadas
        $rowCount = $stmt->rowCount();

        // Devolver una respuesta al frontend indicando que los registros se eliminaron exitosamente
        echo "Se eliminaron $rowCount registros exitosamente.";
    } catch (PDOException $e) {
        // En caso de error en la consulta, mostrar el mensaje y detener el script
        die("Error al eliminar registros: " . $e->getMessage());
    }
} else {
    // Si no se recibieron los IDs o no son válidos, devolver un mensaje de error al frontend
    echo "No se proporcionaron IDs válidos para eliminar registros.";
}
?>
