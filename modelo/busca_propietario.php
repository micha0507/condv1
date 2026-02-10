<?php
include './conexion.php';

if (isset($_GET['query'])) {
    $query = $conexion->real_escape_string($_GET['query']);

    $sql = "SELECT id, rif, nombre, apellido FROM propietario WHERE rif LIKE ? OR nombre LIKE ? OR apellido LIKE ? LIMIT 10";
    $stmt = $conexion->prepare($sql);
    $search = "%$query%";
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $propietarios = [];

    while ($row = $result->fetch_assoc()) {
        $id_propietario = $row['id'];

        // Buscar residencias
        $sql_res = "SELECT id, nro FROM residencias WHERE id_propietario = ?";
        $stmt_res = $conexion->prepare($sql_res);
        $stmt_res->bind_param("i", $id_propietario);
        $stmt_res->execute();
        $res_result = $stmt_res->get_result();

        $residencias = [];
        while ($res_row = $res_result->fetch_assoc()) {
            $id_residencia = $res_row['id'];

            // Buscar facturas pendientes
            $sql_f = "SELECT id_factura, monto FROM facturas WHERE id_residencia = ? AND status = 'Pendiente'";
            $stmt_f = $conexion->prepare($sql_f);
            $stmt_f->bind_param("i", $id_residencia);
            $stmt_f->execute();
            $f_res = $stmt_f->get_result();

            $facturas = [];
            while ($f_row = $f_res->fetch_assoc()) {
                $facturas[] = ['id_factura' => $f_row['id_factura'], 'monto' => $f_row['monto']];
            }
            $stmt_f->close();

            $residencias[] = ['id' => $id_residencia, 'nro' => $res_row['nro'], 'facturas' => $facturas];
        }
        $stmt_res->close();

        // IMPORTANTE: Se agrega el campo 'id' al JSON final
        $propietarios[] = [
            'id' => $row['id'],
            'rif' => $row['rif'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'residencias' => $residencias
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($propietarios);
    $stmt->close();
}
