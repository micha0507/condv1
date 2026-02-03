<?php
include './conexion.php';

if (isset($_GET['query'])) {
    $query = $conexion->real_escape_string($_GET['query']);

    // 1. Buscamos a los propietarios que coincidan con el RIF, nombre o apellido
    $sql = "SELECT id, rif, nombre, apellido FROM propietario WHERE rif LIKE ? OR nombre LIKE ? OR apellido LIKE ? LIMIT 10";
    $stmt = $conexion->prepare($sql);
    $search = "%$query%";
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $propietarios = [];

    while ($row = $result->fetch_assoc()) {
        $id_propietario = $row['id'];

        // 2. Por cada propietario, buscamos sus residencias
        $sql_res = "SELECT id, nro FROM residencias WHERE id_propietario = ?";
        $stmt_res = $conexion->prepare($sql_res);
        $stmt_res->bind_param("i", $id_propietario);
        $stmt_res->execute();
        $res_result = $stmt_res->get_result();

        $residencias = [];
        while ($res_row = $res_result->fetch_assoc()) {
            $id_residencia = $res_row['id'];
            $nro_residencia = $res_row['nro'];

            // 3. Buscamos las facturas pendientes para esta residencia
            // Asegúrate de que la tabla se llame 'facturas' y tenga los campos correctos
            $sql_facturas = "SELECT id_factura, monto FROM facturas WHERE id_residencia = ? AND status = 'Pendiente'";
            $stmt_facturas = $conexion->prepare($sql_facturas);
            $stmt_facturas->bind_param("i", $id_residencia);
            $stmt_facturas->execute();
            $facturas_result = $stmt_facturas->get_result();

            $facturas = [];
            while ($factura_row = $facturas_result->fetch_assoc()) {
                $facturas[] = [
                    'id_factura' => $factura_row['id_factura'],
                    'monto' => $factura_row['monto']
                ];
            }
            $stmt_facturas->close();

            // Guardamos la residencia con sus facturas
            $residencias[] = [
                'id' => $id_residencia,
                'nro' => $nro_residencia,
                'facturas' => $facturas
            ];
        }

        // Estructuramos la respuesta final para el JavaScript
        $propietarios[] = [
            'rif' => $row['rif'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'residencias' => $residencias
        ];

        $stmt_res->close();
    }

    // Enviamos el JSON completo
    header('Content-Type: application/json');
    echo json_encode($propietarios);

    $stmt->close();
    $conexion->close();
}
