<?php
// filepath: c:\xampp\htdocs\condv1\modelo\procesar_pago.php
include './conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos recibidos
    $propietario_id = isset($_POST['propietario_id']) ? intval($_POST['propietario_id']) : 0;
    $fecha = isset($_POST['fecha']) ? $conexion->real_escape_string($_POST['fecha']) : '';
    $status = isset($_POST['status']) ? $conexion->real_escape_string($_POST['status']) : '';
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $referencia = isset($_POST['referencia']) ? $conexion->real_escape_string($_POST['referencia']) : '';

    // Validar que los campos requeridos no estén vacíos
    if ($propietario_id > 0 && !empty($fecha) && !empty($status) && $monto > 0 && !empty($referencia)) {
        // Insertar el pago en la base de datos
        $query = "INSERT INTO pagos (id_propietario, fecha, status, monto, referencia) 
                  VALUES ($propietario_id, '$fecha', '$status', $monto, '$referencia')";

        if ($conexion->query($query) === TRUE) {
            header("Location: ../pago_registrado.html");
            exit();
        } else {
            echo "Error al registrar el pago: " . $conexion->error;
        }
    } else {
        echo "Por favor, complete todos los campos requeridos.";
    }
} else {
    echo "Método de solicitud no válido.";
}

$conexion->close();
?>