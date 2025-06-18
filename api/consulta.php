<?php
// Permitir peticiones desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include '../modelo/conexion.php';

// INNER JOIN con la tabla propietarios para traer nombre, apellido y cedula
$sql = "SELECT 
            pagos.id, 
            pagos.fecha, 
            pagos.fecha_registro, 
            pagos.status, 
            pagos.id_propietario, 
            pagos.monto, 
            pagos.referencia, 
            pagos.factura_afectada,
            propietario.nombre,
            propietario.apellido,
            propietario.rif
        FROM pagos
        INNER JOIN propietario ON pagos.id_propietario = propietario.id
        ORDER BY pagos.fecha DESC";

$result = $conexion->query($sql);

$pagos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pagos[] = $row;
    }
}

// Cerrar conexión
$conexion->close();

// Responder directamente el array
echo json_encode($pagos);