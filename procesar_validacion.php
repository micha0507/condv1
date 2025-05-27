<?php
// filepath: c:\xampp\htdocs\condv1\procesar_validacion.php
include './modelo/conexion.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['validar'])) {
    // Obtener los IDs de los pagos seleccionados
    $ids = $_POST['validar']; // Array de IDs seleccionados
    $ids = array_map('intval', $ids); // Asegurarse de que sean enteros
    $ids_list = implode(',', $ids); // Convertir el array en una lista separada por comas

    // Actualizar el estado de los pagos seleccionados a "Validado"
    $sql_pagos = "UPDATE pagos SET status = 'Validado' WHERE id IN ($ids_list)";

    if ($conexion->query($sql_pagos)) {
        // Actualizar el estado de las facturas relacionadas a "Pagado"
        $sql_facturas = "
            UPDATE facturas
            SET status = 'Pagado'
            WHERE id_factura IN (
                SELECT factura_afectada
                FROM pagos
                WHERE id IN ($ids_list) AND factura_afectada IS NOT NULL
            )
        ";

        if ($conexion->query($sql_facturas)) {
            // Redirigir con un mensaje de éxito
            header("Location: pagos.php?mensaje=Pagos y facturas actualizados correctamente");
            exit();
        } else {
            // Mostrar un mensaje de error si la consulta de facturas falla
            echo "Error al actualizar las facturas: " . $conexion->error;
        }
    } else {
        // Mostrar un mensaje de error si la consulta de pagos falla
        echo "Error al actualizar los pagos: " . $conexion->error;
    }
} else {
    // Redirigir si no se seleccionaron pagos
    header("Location: pagos.php?mensaje=No se seleccionaron pagos para validar");
    exit();
}

$conexion->close();
?>