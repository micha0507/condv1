<?php
include './conexion.php';

// 1. Definir el periodo actual para la validación
$periodo_actual = date('m-Y');

// Consultar todas las residencias
$query_residencias = "SELECT id AS id_residencia, id_propietario FROM residencias";
$result_residencias = $conexion->query($query_residencias);

if ($result_residencias && $result_residencias->num_rows > 0) {
    while ($residencia = $result_residencias->fetch_assoc()) {
        $id_residencia = $residencia['id_residencia'];
        $id_propietario = $residencia['id_propietario'];

        // Verificar si el propietario existe
        $query_verificar_propietario = "SELECT id FROM propietario WHERE id = '$id_propietario'";
        $result_verificar = $conexion->query($query_verificar_propietario);

        if ($result_verificar && $result_verificar->num_rows > 0) {
            
            // --- NUEVA LÓGICA DE VALIDACIÓN ---
            
            // A. Verificar si tiene facturas con estatus 'Pendiente' (de cualquier mes)
            $query_pendientes = "SELECT id_factura FROM facturas 
                                 WHERE propietario_id = '$id_propietario' 
                                 AND status = 'Pendiente'";
            $res_pendientes = $conexion->query($query_pendientes);

            // B. Verificar si ya pagó el periodo actual (Estatus 'Validado' en tabla pagos)
            // Se asocia mediante la factura_afectada que guarda el ID de la factura
            $query_pagado = "SELECT p.id FROM pagos p
                             INNER JOIN facturas f ON p.factura_afectada = f.id_factura
                             WHERE f.propietario_id = '$id_propietario' 
                             AND f.periodo = '$periodo_actual' 
                             AND p.status = 'Validado'";
            $res_pagado = $conexion->query($query_pagado);

            if ($res_pendientes->num_rows > 0) {
                echo "Salteado: El propietario ID $id_propietario ya tiene facturas PENDIENTES.<br>";
                continue; // No genera y pasa al siguiente
            }

            if ($res_pagado->num_rows > 0) {
                echo "Salteado: El periodo $periodo_actual ya fue PAGADO y VALIDADO por el propietario ID $id_propietario.<br>";
                continue; // No genera y pasa al siguiente
            }

            // --- FIN DE VALIDACIONES ---

            // Consultar el monto mensual desde la tabla factor
            $query_monto = "SELECT monto_mensual FROM factor ORDER BY id DESC LIMIT 1";
            $result_monto = $conexion->query($query_monto);

            if ($result_monto && $result_monto->num_rows > 0) {
                $monto = $result_monto->fetch_assoc()['monto_mensual'];
                $fecha_vencimiento = date('Y-m-d', strtotime('+15 days'));

                // Insertar la factura solo si pasó las validaciones anteriores
                $query_factura = "INSERT INTO facturas (propietario_id, fecha_vencimiento, periodo, monto, id_residencia, status) 
                                  VALUES ('$id_propietario', '$fecha_vencimiento', '$periodo_actual', '$monto', '$id_residencia', 'Pendiente')";

                if ($conexion->query($query_factura) === TRUE) {
                    echo "Factura generada exitosamente para la residencia ID: $id_residencia (Periodo: $periodo_actual)<br>";
                } else {
                    echo "Error al generar factura para la residencia ID: $id_residencia - " . $conexion->error . "<br>";
                }
            }
        }
    }
} else {
    echo "No se encontraron residencias para procesar.";
}
?>
