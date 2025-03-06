<?php
include 'modelo/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rif_cedula = strtoupper($_POST['rif_cedula']);

    $sql = "SELECT id, nombre, apellido FROM propietario WHERE rif = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    $stmt->bind_param("s", $rif_cedula);
    $stmt->execute();
    $stmt->bind_result($id_propietario, $nombre, $apellido);
    $stmt->fetch();

    if ($id_propietario) {
        echo json_encode(['id' => $id_propietario, 'nombre' => $nombre, 'apellido' => $apellido]);
    } else {
        echo json_encode(['error' => 'Propietario no encontrado']);
    }

    $stmt->close();
    $conexion->close();
}
?>