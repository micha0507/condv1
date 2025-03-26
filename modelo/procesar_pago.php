<?php
// filepath: c:\xampp\htdocs\condv1\modelo\procesar_pago.php
include './conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos recibidos del formulario
    $fecha_registro = isset($_POST['fecha']) ? $conexion->real_escape_string($_POST['fecha']) : null;
    $status = isset($_POST['status']) ? $conexion->real_escape_string($_POST['status']) : null;
    $nro_residencia = isset($_POST['residencia_id']) ? $conexion->real_escape_string($_POST['residencia_id']) : null;
    $id_propietario = isset($_POST['propietario_id']) ? $conexion->real_escape_string($_POST['propietario_id']) : null;
    $monto = isset($_POST['monto']) ? $conexion->real_escape_string($_POST['monto']) : null;
    $referencia = isset($_POST['referencia']) ? $conexion->real_escape_string($_POST['referencia']) : null;

    // Verificar que todos los campos requeridos estén completos
    if ($fecha_registro && $status && $nro_residencia && $id_propietario && $monto && $referencia) {
        // Preparar la consulta SQL para insertar los datos en la tabla pagos
        $query = "INSERT INTO pagos (fecha, status, nro_residencia, id_propietario, monto, referencia) 
                  VALUES ('$fecha_registro', '$status', '$nro_residencia', '$id_propietario', '$monto', '$referencia')";

        // Ejecutar la consulta
        if ($conexion->query($query) === TRUE) {
            header("Location: ../pago_registrado.php");
            exit();
        } else {
            echo "Error al registrar el pago: " . $conexion->error;
        }
    } else {
        echo "Por favor, complete todos los campos del formulario.";
    }
} else {
    echo "Método de solicitud no válido.";
}

// Cerrar la conexión
$conexion->close();
?>