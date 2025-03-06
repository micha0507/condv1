<?php
include 'modelo/conexion.php';

function buscarResidenciasPorPropietario($id_propietario) {
    global $conexion;

    $sql = "SELECT nro FROM residencias WHERE id_propietario = ?";
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    $stmt->bind_param("i", $id_propietario);
    $stmt->execute();
    $stmt->bind_result($nro);

    $residencias = [];
    while ($stmt->fetch()) {
        $residencias[] = $nro;
    }

    $stmt->close();
    $conexion->close();

    return $residencias;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_propietario = $_POST['id_propietario'];
    $residencias = buscarResidenciasPorPropietario($id_propietario);

    if (!empty($residencias)) {
        echo json_encode(['residencias' => $residencias]);
    } else {
        echo json_encode(['error' => 'No se encontraron residencias para este propietario']);
    }
}
