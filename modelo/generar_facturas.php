<?php
include './conexion.php';

// Consultar todas las residencias
$query_residencias = "SELECT id AS id_residencia, id_propietario FROM residencias";
$result_residencias = $conexion->query($query_residencias);

if ($result_residencias && $result_residencias->num_rows > 0) {
    while ($residencia = $result_residencias->fetch_assoc()) {
        $id_residencia = $residencia['id_residencia'];
        $id_propietario = $residencia['id_propietario'];

        // Verificar si el propietario existe en la tabla propietario
        $query_verificar_propietario = "SELECT id FROM propietario WHERE id = '$id_propietario'";
        $result_verificar = $conexion->query($query_verificar_propietario);

        if ($result_verificar && $result_verificar->num_rows > 0) {
            // Consultar el monto mensual desde la tabla factor
            $query_monto = "SELECT monto_mensual FROM factor LIMIT 1";
            $result_monto = $conexion->query($query_monto);

            if ($result_monto && $result_monto->num_rows > 0) {
                $monto = $result_monto->fetch_assoc()['monto_mensual'];

                // Generar fecha de vencimiento (15 días después de hoy)
                $fecha_vencimiento = date('Y-m-d', strtotime('+15 days'));

                // Generar el periodo en formato "MM-YYYY"
                $periodo = date('m-Y');

                // Insertar la factura en la tabla facturas
                $query_factura = "INSERT INTO facturas (propietario_id, fecha_vencimiento, periodo, monto, id_residencia, status) 
                                  VALUES ('$id_propietario', '$fecha_vencimiento', '$periodo', '$monto', '$id_residencia', 'pendiente')";

                if ($conexion->query($query_factura) === TRUE) {
                    echo "Factura generada para la residencia ID: $id_residencia<br>";
                } else {
                    echo "Error al generar factura para la residencia ID: $id_residencia - " . $conexion->error . "<br>";
                }
            } else {
                echo "No se pudo obtener el monto mensual de la tabla factor. Factura no generada para la residencia ID: $id_residencia.<br>";
            }
        } else {
            echo "Propietario no encontrado para la residencia ID: $id_residencia. Factura no generada.<br>";
        }
    }
} else {
    echo "No se encontraron residencias para generar facturas.";
}

// Cerrar la conexión
$conexion->close();
?>
