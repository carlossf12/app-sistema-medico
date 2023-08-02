<?php
require_once('../../backend/db/config.php');

// Verificar si el formulario ha sido enviado
    $id_specialty = $_POST['id_specialty'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $data_update = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("UPDATE specialty SET name = :name, description = :description, data_update = :data_update WHERE id_specialty = :id_specialty");
    $stmt->bindParam(':id_specialty', $id_specialty);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':data_update', $data_update);

    if ($stmt->execute()) {
        echo 'Area actualizada.';
    } else {
        $error = $stmt->errorInfo();
        die("Query failed: " . $error[2]);
    }
?>
