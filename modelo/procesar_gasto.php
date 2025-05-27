<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $concepto = $conexion->real_escape_string($_POST['concepto']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);
    $monto = $conexion->real_escape_string($_POST['monto']);
    $fecha = $conexion->real_escape_string($_POST['fecha']); // Nuevo campo

    // Preparar la consulta SQL para insertar los datos
    $sql = "INSERT INTO gastos_eventuales (concepto, categoria, monto, fecha) VALUES ('$concepto', '$categoria', '$monto', '$fecha')";

    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) {
        echo "Gasto registrado exitosamente.";
    } else {
        echo "Error al registrar el gasto: " . $conexion->error;
    }

    // Cerrar la conexión
    $conexion->close();
} else {
    echo "Método de solicitud no válido.";
}
?>