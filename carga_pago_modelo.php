<?php
include 'modelo/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_propietario = $_POST['id_propietario'];
    $nro_residencia = $_POST['nro_residencia'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $referencia = $_POST['referencia'];
    $status = $_POST['status'];

    $sql = "INSERT INTO pagos (id_propietario, nro_residencia, monto, fecha, referencia, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        die(json_encode(['error' => 'Error en la preparación de la consulta: ' . $conexion->error]));
    }
    $stmt->bind_param("iissss", $id_propietario, $nro_residencia, $monto, $fecha, $referencia, $status);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Pago cargado exitosamente']);
    } else {
        echo json_encode(['error' => 'Error al ejecutar la consulta: ' . $stmt->error]);
    }

    $stmt->close();
    $conexion->close();
}
?>