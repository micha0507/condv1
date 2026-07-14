<?php
include './modelo/conexion.php';
session_start();

if (empty($_SESSION['id'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}

function getPropietarioId($conexion) {
    if (!empty($_SESSION['id_propietario'])) return $_SESSION['id_propietario'];
    if (!empty($_SESSION['id'])) return $_SESSION['id'];
    return null;
}

$id_propietario = getPropietarioId($conexion);

$msg = '';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cargar_pago') {
    $factura_id = isset($_POST['factura_id']) ? intval($_POST['factura_id']) : 0;
    $residencia_id = isset($_POST['residencia_id']) ? intval($_POST['residencia_id']) : 0;
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $fecha_pago = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d');
    $referencia = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';

    if ($factura_id <= 0 || $monto <= 0) {
        $err = 'Seleccione una factura válida y un monto mayor a 0.';
    } else {
        $stmtF = $conexion->prepare('SELECT monto FROM facturas WHERE id_factura = ? AND propietario_id = ? LIMIT 1');
        $stmtF->bind_param('ii', $factura_id, $id_propietario);
        $stmtF->execute();
        $factRow = $stmtF->get_result()->fetch_assoc();

        if ($factRow) {
            $nro_res = '';
            if ($residencia_id > 0) {
                $stmtR = $conexion->prepare('SELECT nro FROM residencias WHERE id = ? AND id_propietario = ? LIMIT 1');
                $stmtR->bind_param('ii', $residencia_id, $id_propietario);
                $stmtR->execute();
                $resR = $stmtR->get_result()->fetch_assoc();
                if ($resR) $nro_res = $resR['nro'];
            }

            $stmtP = $conexion->prepare('INSERT INTO pagos (fecha, fecha_registro, status, id, id_propietario, monto, referencia, factura_afectada) VALUES (?, NOW(), "pendiente", ?, ?, ?, ?, ?)');
            $stmtP->bind_param('ssidss', $fecha_pago, $nro_res, $id_propietario, $monto, $referencia, $factura_id);
            if ($stmtP->execute()) {
                if ($monto >= floatval($factRow['monto'])) {
                    $stmtU = $conexion->prepare('UPDATE facturas SET status = "pagado" WHERE id_factura = ?');
                    $stmtU->bind_param('i', $factura_id);
                    $stmtU->execute();
                }
                $msg = '¡Pago registrado exitosamente! En espera de validación.';
            } else {
                $err = 'Error al registrar pago.';
            }
        }
    }
}

// Consultas
$stmtUser = $conexion->prepare("SELECT nombre, apellido, rol FROM propietario WHERE id = ?");
$stmtUser->bind_param("i", $id_propietario);
$stmtUser->execute();
$userData = $stmtUser->get_result()->fetch_assoc();

$stmtFact = $conexion->prepare("SELECT * FROM facturas WHERE propietario_id = ? AND status NOT IN ('pagado', 'validado') ORDER BY fecha_vencimiento ASC");
$stmtFact->bind_param("i", $id_propietario);
$stmtFact->execute();
$facturas_pendientes = $stmtFact->get_result();

$stmtHist = $conexion->prepare("SELECT * FROM pagos WHERE id_propietario = ? AND status = 'validado' ORDER BY fecha DESC LIMIT 5");
$stmtHist->bind_param("i", $id_propietario);
$stmtHist->execute();
$historico_pagos = $stmtHist->get_result();

$residencias = $conexion->query("SELECT id, nro FROM residencias WHERE id_propietario = $id_propietario");

$resFactor = $conexion->query("SELECT factor, fecha FROM factor ORDER BY id DESC LIMIT 1");
$factor_data = $resFactor->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal | Conjunto Residencial INDULAC</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #27ae60;
            --primary-dark: #219150;
            --secondary: #2c3e50;
            --bg-color: #f4f7f6;
            --text-main: #333;
            --text-muted: #7f8c8d;
            --card-bg: #fff;
            --danger: #e74c3c;
            --danger-bg: #fadbd8;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            color: var(--text-main);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Encabezado */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--card-bg);
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            border-left: 5px solid var(--primary);
        }

        .user-info h2 { margin: 0; font-size: 1.5rem; color: var(--secondary); }
        .user-info p { margin: 5px 0 0; color: var(--text-muted); font-size: 0.9rem; }
        
        .factor-info { text-align: right; }
        .factor-info h3 { margin: 0; color: var(--primary); font-size: 1.8rem; }
        .factor-info small { color: var(--text-muted); font-size: 0.8rem; }

        /* Grid Layout */
        .grid-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .card h3 {
            margin-top: 0;
            color: var(--secondary);
            font-size: 1.2rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Alertas */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }

        /* Tablas */
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: var(--secondary); color: white; font-weight: 500; }
        tbody tr:hover { background-color: #f9f9f9; }

        /* Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-success { background: #d4edda; color: #155724; }

        /* Formularios */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 8px; color: var(--secondary); font-size: 0.95rem;}
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
            transition: 0.3s;
        }
        .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1); }
        .form-control[readonly] { background-color: #f8f9fa; color: var(--primary); font-weight: 600; font-size: 1.1rem;}

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-submit:hover { background: var(--primary-dark); }
        
        /* Widget Morosos */
        .widget-moroso {
            background: var(--danger-bg);
            border-left: 5px solid var(--danger);
            padding: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        .widget-moroso h1 { margin: 0; font-size: 2.5rem; color: var(--danger); line-height: 1; }
        .widget-moroso p { margin: 0; color: #721c24; font-weight: 600; }

    </style>
</head>
<body>

    <?php include 'navbar_propietario.php'; ?>

    <div class="container">
        
        <div class="dashboard-header">
            <div class="user-info">
                <p>Bienvenido al sistema</p>
                <h2><?= htmlspecialchars($userData['nombre'] . ' ' . $userData['apellido']) ?></h2>
                <span class="badge badge-success"><?= htmlspecialchars($userData['rol']) ?></span>
            </div>
            <div class="factor-info">
                <p style="margin:0; color: var(--text-muted); font-size: 0.9rem;">Factor del día (BCV)</p>
                <h3><?= number_format($factor_data['factor'], 2) ?> Bs</h3>
                <small>Actualizado: <?= date("d/m/Y", strtotime($factor_data['fecha'])) ?></small>
            </div>
        </div>

        <?php if ($msg): ?> <div class="alert alert-success"><span class="material-symbols-outlined" style="vertical-align: middle;">check_circle</span> <?= $msg ?></div> <?php endif; ?>
        <?php if ($err): ?> <div class="alert alert-error"><span class="material-symbols-outlined" style="vertical-align: middle;">error</span> <?= $err ?></div> <?php endif; ?>

        <div class="grid-layout">
            
            <!-- Columna Izquierda: Listados -->
            <div class="main-column">
                
                <div class="widget-moroso">
                    <span class="material-symbols-outlined" style="font-size: 3rem; color: var(--danger);">receipt_long</span>
                    <div>
                        <h1><?= $facturas_pendientes->num_rows ?></h1>
                        <p>Facturas pendientes por pagar</p>
                    </div>
                </div>

                <div class="card">
                    <h3>Detalle de Facturas Pendientes</h3>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Vencimiento</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($facturas_pendientes->num_rows > 0): ?>
                                    <?php while ($f = $facturas_pendientes->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?= $f['id_factura'] ?></strong></td>
                                            <td><?= date("d/m/Y", strtotime($f['fecha_vencimiento'])) ?></td>
                                            <td style="color: var(--danger); font-weight: 600;"><?= number_format($f['monto'], 2) ?> $</td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    <?= $f['status'] == 'pendiente' ? 'Por Notificar' : htmlspecialchars($f['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No tiene facturas pendientes.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                                <div class="card">
                    <h3>Últimos Pagos Validados</h3>
                    <table style="font-size: 0.9rem;">
                        <?php if ($historico_pagos->num_rows > 0): ?>
                            <?php while ($hp = $historico_pagos->fetch_assoc()): ?>
                                <tr>
                                    <td style="padding: 8px 0;"><?= date("d/m/y", strtotime($hp['fecha'])) ?></td>
                                    <td style="text-align: right; font-weight: 600; padding: 8px 0;"><?= number_format($hp['monto'], 2) ?> Bs</td>
                                    <td style="text-align: right; padding: 8px 0;"><span class="material-symbols-outlined" style="color: var(--primary); font-size: 1.2rem; vertical-align: middle;">check_circle</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding: 10px 0;">Sin pagos recientes.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Columna Derecha: Formulario y Widget -->
            <div class="side-column">
                
                <div class="card" style="margin-bottom: 25px;">
                    <h3><span class="material-symbols-outlined" style="vertical-align: bottom;">payments</span> Cargar Nuevo Pago</h3>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="cargar_pago">

                        <div class="form-group">
                            <label>Seleccionar Factura</label>
                            <select name="factura_id" id="select_factura" class="form-control" required onchange="calcularMonto()">
                                <option value="">-- Seleccione factura --</option>
                                <?php
                                $facturas_pendientes->data_seek(0);
                                while ($ff = $facturas_pendientes->fetch_assoc()): ?>
                                    <option value="<?= $ff['id_factura'] ?>" data-monto="<?= $ff['monto'] ?>">
                                        Factura #<?= $ff['id_factura'] ?> - <?= number_format($ff['monto'], 2, ',', '.') ?> $
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Monto a Pagar (Bs)</label>
                            <input type="text" id="monto_formateado" class="form-control" placeholder="0,00" readonly>
                            <input type="hidden" name="monto" id="monto_real">
                        </div>

                        <div class="form-group">
                            <label>Inmueble / Residencia</label>
                            <select name="residencia_id" class="form-control">
                                <option value="0">-- No especificar --</option>
                                <?php
                                $residencias->data_seek(0);
                                while ($r = $residencias->fetch_assoc()): ?>
                                    <option value="<?= $r['id'] ?>">Inmueble <?= $r['nro'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Referencia Bancaria</label>
                            <input type="text" name="referencia" class="form-control" placeholder="Ej. 12345678" required>
                        </div>

                        <div class="form-group">
                            <label>Fecha de Transferencia</label>
                            <input type="date" name="fecha_pago" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <button type="submit" class="btn-submit">Notificar Pago</button>
                    </form>
                </div>



            </div>
        </div>
    </div>

    <script>
        const factorDia = <?= $factor_data['factor'] ?? 1 ?>;

        function calcularMonto() {
            const select = document.getElementById('select_factura');
            const inputReal = document.getElementById('monto_real');
            const inputVisible = document.getElementById('monto_formateado');
            const optionSeleccionada = select.options[select.selectedIndex];
            const montoUSD = optionSeleccionada.getAttribute('data-monto');

            if (montoUSD) {
                const totalBs = parseFloat(montoUSD) * factorDia;
                inputReal.value = totalBs.toFixed(2);
                inputVisible.value = new Intl.NumberFormat('es-VE', {
                    style: 'currency', currency: 'VES'
                }).format(totalBs).replace('VES', '').trim();
            } else {
                inputReal.value = "";
                inputVisible.value = "";
            }
        }
    </script>
</body>
</html>