<?php
// filepath: c:\xampp\htdocs\condv1\modelo\procesar_pago.php
include './conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos recibidos del formulario
    $fecha_registro = isset($_POST['fecha']) ? $conexion->real_escape_string($_POST['fecha']) : null;
    $status = isset($_POST['status']) ? $conexion->real_escape_string($_POST['status']) : null;
    $id_propietario = isset($_POST['propietario_id']) ? $conexion->real_escape_string($_POST['propietario_id']) : null;
    $referencia = isset($_POST['referencia']) ? $conexion->real_escape_string($_POST['referencia']) : null;
    $factura_afectada = isset($_POST['factura_afectada']) ? $conexion->real_escape_string($_POST['factura_afectada']) : null;
    $monto = isset($_POST['monto']) ? $conexion->real_escape_string($_POST['monto']) : null; // Obtener el monto enviado desde el formulario

    // Verificar que todos los campos requeridos estén completos
    if ($fecha_registro && $status && $id_propietario && $referencia && $factura_afectada && $monto) {
        // Preparar la consulta SQL para insertar los datos en la tabla pagos
        $query = "INSERT INTO pagos (fecha, fecha_registro, status, id_propietario, monto, referencia, factura_afectada) 
                  VALUES (NOW(), '$fecha_registro', '$status', '$id_propietario', '$monto', '$referencia', '$factura_afectada')";

        // Ejecutar la consulta
        if ($conexion->query($query)) {
            // Redirigir al archivo pago_registrado.php si se registra correctamente
            header("Location: ../pago_registrado.php");
            exit();
        } else {
            echo "Error al registrar el pago: " . $conexion->error;
        }
    } else {
        echo "Faltan datos requeridos para registrar el pago.";
    }
} else {
    // Redirigir al archivo pago_registrado.php si el método no es POST
    header("Location: ../pago_registrado.php");
    exit();
}

// Cerrar la conexión
$conexion->close();
?>