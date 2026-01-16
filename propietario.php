<?php
session_start();
require_once __DIR__ . '/modelo/conexion.php';

function getPropietarioId($conexion) {
    if (!empty($_SESSION['id_propietario'])) return $_SESSION['id_propietario'];
    if (!empty($_SESSION['id'])) return $_SESSION['id'];
    if (!empty($_SESSION['email_propietario'])) {
        $email = $_SESSION['email_propietario'];
    } elseif (!empty($_SESSION['email'])) {
        $email = $_SESSION['email'];
    } else {
        $email = null;
    }
    if ($email) {
        $stmt = $conexion->prepare("SELECT id FROM propietario WHERE email_propietario = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) return $row['id'];
    }
    return null;
}

$id_propietario = getPropietarioId($conexion);
if (!$id_propietario) {
    header('Location: login.php');
    exit;
}

$msg = '';
$err = '';

// Manejo de envío de pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cargar_pago') {
    $factura_id = isset($_POST['factura_id']) ? intval($_POST['factura_id']) : 0;
    $residencia_id = isset($_POST['residencia_id']) ? intval($_POST['residencia_id']) : 0;
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $fecha_pago = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d');
    $referencia = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';

    if ($factura_id <= 0 || $monto <= 0) {
        $err = 'Seleccione una factura válida y un monto mayor a 0.';
    } else {
        // obtener monto de la factura
        $stmtF = $conexion->prepare('SELECT monto FROM facturas WHERE id_factura = ? AND propietario_id = ? LIMIT 1');
        $stmtF->bind_param('ii', $factura_id, $id_propietario);
        $stmtF->execute();
        $resF = $stmtF->get_result();
        if (!$factRow = $resF->fetch_assoc()) {
            $err = 'Factura no encontrada.';
        } else {
            $fact_monto = floatval($factRow['monto']);

            // obtener nro de residencia seleccionado
            $nro_res = '';
            if ($residencia_id > 0) {
                $stmtR = $conexion->prepare('SELECT nro FROM residencias WHERE id = ? AND id_propietario = ? LIMIT 1');
                $stmtR->bind_param('ii', $residencia_id, $id_propietario);
                $stmtR->execute();
                $resR = $stmtR->get_result();
                if ($r = $resR->fetch_assoc()) $nro_res = $r['nro'];
            }

            // Insertar en pagos
            $status_pago = 'pendiente';
            $stmtP = $conexion->prepare('INSERT INTO pagos (fecha, fecha_registro, status, nro_residencia, id_propietario, monto, referencia) VALUES (?, NOW(), ?, ?, ?, ?, ?)');
            $stmtP->bind_param('sssdss', $fecha_pago, $status_pago, $nro_res, $id_propietario, $monto, $referencia);
            if ($stmtP->execute()) {
                // actualizar factura si el monto cubre la factura
                if ($monto >= $fact_monto) {
                    $nuevoStatus = 'pagado';
                    $stmtU = $conexion->prepare('UPDATE facturas SET status = ? WHERE id_factura = ? AND propietario_id = ?');
                    $stmtU->bind_param('sii', $nuevoStatus, $factura_id, $id_propietario);
                    $stmtU->execute();
                }
                $msg = 'Pago cargado correctamente.';
            } else {
                $err = 'Error al registrar el pago.';
            }
        }
    }
}

// Obtener facturas pendientes
$stmt = $conexion->prepare('SELECT id_factura, propietario_id, fecha_emision, fecha_vencimiento, monto, status FROM facturas WHERE propietario_id = ? AND status != "pagado" ORDER BY fecha_vencimiento ASC');
$stmt->bind_param('i', $id_propietario);
$stmt->execute();
$facturas = $stmt->get_result();

// Obtener residencias del propietario
$stmtR = $conexion->prepare('SELECT id, nro FROM residencias WHERE id_propietario = ?');
$stmtR->bind_param('i', $id_propietario);
$stmtR->execute();
$residencias = $stmtR->get_result();

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Propietario - Facturas pendientes</title>
   <link rel="icon" href="/img/ico_condo.ico">
</head>
<body>
    <div class="container">
        <?php
        // Obtener nombre, apellido y rol desde la sesión o base de datos
        $nombre = $apellido = $rol = '';
        if (!empty($_SESSION['nombre'])) $nombre = $_SESSION['nombre'];
        if (!empty($_SESSION['apellido'])) $apellido = $_SESSION['apellido'];
        if (!empty($_SESSION['rol'])) $rol = $_SESSION['rol'];

        if (($nombre === '' || $apellido === '' || $rol === '') && $id_propietario) {
            $stmtU = $conexion->prepare('SELECT nombre, apellido, rol FROM propietario WHERE id = ? LIMIT 1');
            $stmtU->bind_param('i', $id_propietario);
            $stmtU->execute();
            $resU = $stmtU->get_result();
            if ($rowU = $resU->fetch_assoc()) {
                if ($nombre === '' && isset($rowU['nombre'])) $nombre = $rowU['nombre'];
                if ($apellido === '' && isset($rowU['apellido'])) $apellido = $rowU['apellido'];
                if ($rol === '' && isset($rowU['rol'])) $rol = $rowU['rol'];
            }
        }
        ?>
        <div class="user-info">
            <?php if ($nombre || $apellido): ?>
                <p>Usuario: <?=htmlspecialchars(trim($nombre . ' ' . $apellido))?></p>
            <?php endif; ?>
            <?php if ($rol): ?>
                <p>Rol: <?=htmlspecialchars($rol)?></p>
            <?php endif; ?>
        </div>
        <h2>Facturas pendientes</h2>
        <?php if ($msg): ?>
            <div style="color:green;"><?=htmlspecialchars($msg)?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div style="color:red;"><?=htmlspecialchars($err)?></div>
        <?php endif; ?>

        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha Emisión</th>
                    <th>Fecha Vencimiento</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($f = $facturas->fetch_assoc()): ?>
                <tr>
                    <td><?=htmlspecialchars($f['id_factura'])?></td>
                    <td><?=htmlspecialchars($f['fecha_emision'])?></td>
                    <td><?=htmlspecialchars($f['fecha_vencimiento'])?></td>
                    <td><?=number_format($f['monto'],2)?></td>
                    <td><?=htmlspecialchars($f['status'])?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Cargar pago</h3>
        <form method="post" action="">
            <input type="hidden" name="action" value="cargar_pago">
            <div>
                <label>Factura:</label>
                <select name="factura_id" required>
                    <option value="">-- Seleccione --</option>
                    <?php
                    // volver a obtener facturas para el dropdown
                    $stmt2 = $conexion->prepare('SELECT id_factura, fecha_vencimiento, monto FROM facturas WHERE propietario_id = ? AND status != "pagado" ORDER BY fecha_vencimiento ASC');
                    $stmt2->bind_param('i', $id_propietario);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result();
                    while ($ff = $res2->fetch_assoc()):
                    ?>
                    <option value="<?=htmlspecialchars($ff['id_factura'])?>">ID <?=htmlspecialchars($ff['id_factura'])?> - Vence <?=htmlspecialchars($ff['fecha_vencimiento'])?> - Monto <?=number_format($ff['monto'],2)?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Residencia (nro):</label>
                <select name="residencia_id">
                    <option value="0">-- No especificar --</option>
                    <?php while ($r = $residencias->fetch_assoc()): ?>
                        <option value="<?=htmlspecialchars($r['id'])?>"><?=htmlspecialchars($r['nro'])?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Monto pagado:</label>
                <input type="number" step="0.01" name="monto" required>
            </div>
            <div>
                <label>Fecha del pago:</label>
                <input type="date" name="fecha_pago" value="<?=date('Y-m-d')?>">
            </div>
            <div>
                <label>Referencia:</label>
                <input type="text" name="referencia">
            </div>
            <div>
                <button type="submit">Cargar pago</button>
            </div>
        </form>
    </div>
</body>
</html>
