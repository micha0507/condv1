<?php
// filepath: c:\xampp\htdocs\condv1\modelo\procesar_pago.php
include './conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_registro = isset($_POST['fecha']) ? $conexion->real_escape_string($_POST['fecha']) : null;
    $status = isset($_POST['status']) ? $conexion->real_escape_string($_POST['status']) : null;
    $id_propietario = isset($_POST['propietario_id']) ? $conexion->real_escape_string($_POST['propietario_id']) : null;
    $referencia = isset($_POST['referencia']) ? $conexion->real_escape_string($_POST['referencia']) : null;
    $factura_afectada = isset($_POST['factura_afectada']) ? $conexion->real_escape_string($_POST['factura_afectada']) : null;
    $monto = isset($_POST['monto']) ? $conexion->real_escape_string($_POST['monto']) : null;

    if ($fecha_registro && $status && $id_propietario && $referencia && $factura_afectada && $monto) {
        $query = "INSERT INTO pagos (fecha, fecha_registro, status, id_propietario, monto, referencia, factura_afectada) 
                  VALUES (NOW(), '$fecha_registro', '$status', '$id_propietario', '$monto', '$referencia', '$factura_afectada')";

        if ($conexion->query($query)) {
            if ($status === 'Validado') {
                // Actualizar el estado de la factura directamente
                $updateFacturaQuery = "
                    UPDATE facturas 
                    SET status = 'Pagada' 
                    WHERE id_factura = '$factura_afectada'
                ";
                if (!$conexion->query($updateFacturaQuery)) {
                    echo "Error al actualizar el estado de la factura: " . $conexion->error;
                }
            }

            header("Location: ../pago_registrado.php");
            exit();
        } else {
            echo "Error al registrar el pago: " . $conexion->error;
        }
    } else {
        echo "Faltan datos requeridos para registrar el pago.";
    }
} else {
    header("Location: ../pago_registrado.php");
    exit();
}

$conexion->close();
?>
