<?php
include './conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pagos'])) {
    $conexion->begin_transaction();

    try {
        foreach ($_POST['pagos'] as $pago) {
            // Validamos que existan las llaves mínimas para evitar Warnings
            $id_propietario = $pago['id_propietario'] ?? null;
            $factura_afectada = $pago['factura_afectada'] ?? null;
            $monto = $pago['monto'] ?? 0;
            $referencia = $pago['referencia'] ?? '';
            $fecha_registro = $pago['fecha_registro'] ?? date('Y-m-d');
            
            // Requerimiento 7: Status Validado si está tildado
            $status = isset($pago['status']) ? 'Validado' : 'Pendiente';

            if ($id_propietario && $factura_afectada) {
                $sql = "INSERT INTO pagos (fecha, fecha_registro, status, id_propietario, monto, referencia, factura_afectada) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conexion->prepare($sql);
                // Usamos la fecha_registro enviada también para el campo 'fecha'
                $stmt->bind_param("sssidss", $fecha_registro, $fecha_registro, $status, $id_propietario, $monto, $referencia, $factura_afectada);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar registro: " . $stmt->error);
                }

                // Si se valida, actualizamos la factura afectada
                if ($status == 'Validado') {
                    $conexion->query("UPDATE facturas SET status = 'Validado' WHERE id_factura = $factura_afectada");
                }
            }
        }

        $conexion->commit();
        echo "<script>alert('Carga masiva completada con éxito'); window.location.href='../carga_masiva.php';</script>";

    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error crítico: " . $e->getMessage();
    }
}