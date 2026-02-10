<?php
include './conexion.php';
ob_clean();
header('Content-Type: application/json');

if (isset($_GET['query'])) {
    $query = $conexion->real_escape_string($_GET['query']);
    $search = "%$query%";

    $sql = "SELECT id, rif, nombre, apellido FROM propietario WHERE rif LIKE ? OR nombre LIKE ? OR apellido LIKE ? LIMIT 5";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $res_prop = $stmt->get_result();

    $data = [];
    while ($p = $res_prop->fetch_assoc()) {
        $id_p = $p['id'];
        
        // 2. Buscar residencias para este propietario
        $res_sql = "SELECT id, nro FROM residencias WHERE id_propietario = ?";
        $stmt_r = $conexion->prepare($res_sql);
        $stmt_r->bind_param("i", $id_p);
        $stmt_r->execute();
        $res_r = $stmt_r->get_result();
        
        $residencias = [];
        while ($r = $res_r->fetch_assoc()) {
            $id_res = $r['id'];
            // 3. Buscar facturas pendientes para esa residencia
            $fac_sql = "SELECT id_factura, monto FROM facturas WHERE id_residencia = $id_res AND status = 'Pendiente'";
            $res_f = $conexion->query($fac_sql);
            
            $facturas = [];
            while ($f = $res_f->fetch_assoc()) { $facturas[] = $f; }
            
            $residencias[] = ['id' => $r['id'], 'nro' => $r['nro'], 'facturas' => $facturas];
        }
        $p['residencias'] = $residencias;
        $data[] = $p;
    }
    echo json_encode($data);
}
?>