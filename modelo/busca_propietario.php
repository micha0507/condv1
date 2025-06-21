<?php
include './conexion.php';

if (isset($_GET['query'])) {
    $query = $conexion->real_escape_string($_GET['query']);
    $sql = "SELECT rif, nombre, apellido FROM propietario WHERE rif LIKE ? OR nombre LIKE ? OR apellido LIKE ? LIMIT 10";
    $stmt = $conexion->prepare($sql);
    $search = "%$query%";
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $propietarios = [];
    while ($row = $result->fetch_assoc()) {
        $propietarios[] = $row;
    }

    echo json_encode($propietarios);
    $stmt->close();
    $conexion->close();
}
?>