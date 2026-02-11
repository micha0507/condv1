<?php
include './modelo/conexion.php';

session_start();
// Validación de sesión de propietario
if (empty($_SESSION['id'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}

// Función para obtener el ID real del propietario
function getPropietarioId($conexion)
{
    if (!empty($_SESSION['id_propietario'])) return $_SESSION['id_propietario'];
    if (!empty($_SESSION['id'])) return $_SESSION['id'];
    return null;
}

$id_propietario = getPropietarioId($conexion);

// Manejo de carga de pago (Lógica existente preservada)
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

// --- CONSULTAS PARA EL DASHBOARD ---
// 1. Datos del Propietario
$stmtUser = $conexion->prepare("SELECT nombre, apellido, rol FROM propietario WHERE id = ?");
$stmtUser->bind_param("i", $id_propietario);
$stmtUser->execute();
$userData = $stmtUser->get_result()->fetch_assoc();

// 2. Facturas Pendientes
$stmtFact = $conexion->prepare("SELECT * FROM facturas WHERE propietario_id = ? AND status NOT IN ('pagado', 'validado') ORDER BY fecha_vencimiento ASC");
$stmtFact->bind_param("i", $id_propietario);
$stmtFact->execute();
$facturas_pendientes = $stmtFact->get_result();
$total_deuda = 0; // Para sumar el total pendiente

// 3. Histórico de Pagos VALIDADOS
$stmtHist = $conexion->prepare("SELECT * FROM pagos WHERE id_propietario = ? AND status = 'validado' ORDER BY fecha DESC LIMIT 5");
$stmtHist->bind_param("i", $id_propietario);
$stmtHist->execute();
$historico_pagos = $stmtHist->get_result();

// 4. Residencias para el select
$residencias = $conexion->query("SELECT id, nro FROM residencias WHERE id_propietario = $id_propietario");

// 5. Factor de Cambio
$resFactor = $conexion->query("SELECT factor, fecha FROM factor ORDER BY id DESC LIMIT 1");
$factor_data = $resFactor->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Propietario</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="icon" href="/img/ico_condo.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Ajustes específicos para el formulario de pago */
        .form_pago {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .form_group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form_group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .form_group input,
        .form_group select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn_enviar {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn_enviar:hover {
            background: #219150;
        }

        .badge_pendiente {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <?php include 'navbar_propietario.php'; ?>

    <div class="principal">
        <div class="encabezado_principal">
            <div class="caja_titulo">
                <p><strong>Bienvenido</strong></p>
                <h2><?= htmlspecialchars($userData['nombre'] . ' ' . $userData['apellido']) ?></h2>
                <p><?= htmlspecialchars($userData['rol']) ?></p>
            </div>
            <div style="text-align: right;">
                <p><strong>Factor del día:</strong></p>
                <h3 style="color: #27ae60;"><?= number_format($factor_data['factor'], 2) ?> Bs</h3>
                <small>Actualizado: <?= date("d/m/Y", strtotime($factor_data['fecha'])) ?></small>
            </div>
        </div>

        <h1 class="titulo">Estado de Cuenta</h1>

        <div class="panel">
            <div class="first_row">
                <div class="marcos_panel" style="background: #f8d7da; border-left: 5px solid #dc3545;">
                    <div class="para_pendientes">
                        <h1 class="numeros_morosos"><?= $facturas_pendientes->num_rows ?></h1>
                        <p style="color: #721c24; font-weight: bold;">Facturas por pagar</p>
                    </div>
                </div>

                <div class="marcos_panel">
                    <h3 style="margin-bottom: 10px;">Pagos Validados (Recientes)</h3>
                    <table style="width: 100%; font-size: 13px;">
                        <?php if ($historico_pagos->num_rows > 0): ?>
                            <?php while ($hp = $historico_pagos->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 5px;"><?= date("d/m/y", strtotime($hp['fecha'])) ?></td>
                                    <td style="text-align: right; font-weight: bold;"><?= number_format($hp['monto'], 2) ?> Bs</td>
                                    <td style="text-align: right;"><span style="color: green;">✔</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No hay pagos validados recientemente.</p>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <div id="publicaciones" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">

                <div class="tabla_contenedor">
                    <h3>Detalle de Facturas Pendientes</h3>
                    <?php if ($msg): ?> <div style="background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin:10px 0;"><?= $msg ?></div> <?php endif; ?>
                    <?php if ($err): ?> <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin:10px 0;"><?= $err ?></div> <?php endif; ?>

                    <table style="width:100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin-top:10px;">
                        <thead style="background: #2c3e50; color: white;">
                            <tr>
                                <th style="padding: 12px; text-align: left;">ID Factura</th>
                                <th style="padding: 12px;">Vencimiento</th>
                                <th style="padding: 12px;">Monto</th>
                                <th style="padding: 12px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($f = $facturas_pendientes->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #eee; text-align: center;">
                                    <td style="padding: 12px; text-align: left;">#<?= $f['id_factura'] ?></td>
                                    <td><?= date("d/m/Y", strtotime($f['fecha_vencimiento'])) ?></td>
                                    <td style="font-weight: bold; color: #c0392b;"><?= number_format($f['monto'], 2) ?> $</td>
                                    <td>
                                        <?php if ($f['status'] == 'pendiente'): ?>
                                            <span class="badge_pendiente" style="background: #fff3cd; color: #856404;">Pendiente por Notificar</span>
                                        <?php else: ?>
                                            <span class="badge_pendiente"><?= htmlspecialchars($f['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form_pago">
                    <h3 style="border-bottom: 2px solid #27ae60; padding-bottom: 10px;">Cargar Nuevo Pago</h3>
                    <form method="post" action="">
                        <input type="hidden" name="action" value="cargar_pago">

                        <div class="form_group">
                            <label>Seleccionar Factura</label>
                            <select name="factura_id" id="select_factura" required onchange="calcularMonto()">
                                <option value="">-- Seleccione factura --</option>
                                <?php
                                $facturas_pendientes->data_seek(0);
                                while ($ff = $facturas_pendientes->fetch_assoc()): ?>
                                    <option value="<?= $ff['id_factura'] ?>" data-monto="<?= $ff['monto'] ?>">
                                        ID: <?= $ff['id_factura'] ?> (<?= number_format($ff['monto'], 2, ',', '.') ?> $)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form_group">
                            <label>Monto a Pagar en Bolívares</label>
                            <input type="text" id="monto_formateado" placeholder="0,00" readonly
                                style="background-color: #f0f0f0; font-weight: bold; color: #27ae60; font-size: 1.2em;">

                            <input type="hidden" name="monto" id="monto_real">

                            <small id="info_calculo" style="color: #7f8c8d; margin-top: 5px;"></small>
                        </div>

                        <div class="form_group">
                            <label>Residencia</label>
                            <select name="residencia_id">
                                <option value="0">-- No especificar --</option>
                                <?php
                                $residencias->data_seek(0);
                                while ($r = $residencias->fetch_assoc()): ?>
                                    <option value="<?= $r['id'] ?>">Inmueble <?= $r['nro'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form_group">
                            <label>Referencia Bancaria</label>
                            <input type="text" name="referencia" placeholder="Nro de confirmación" required>
                        </div>

                        <div class="form_group">
                            <label>Fecha del Pago</label>
                            <input type="date" name="fecha_pago" value="<?= date('Y-m-d') ?>">
                        </div>

                        <button type="submit" class="btn_enviar">Notificar Pago</button>
                    </form>
                </div>

                <script>
                    // Pasamos el factor de PHP a JS
                    const factorDia = <?= $factor_data['factor'] ?? 1 ?>;

                    function calcularMonto() {
                        const select = document.getElementById('select_factura');
                        const inputReal = document.getElementById('monto_real');
                        const inputVisible = document.getElementById('monto_formateado');
                        const infoCalculo = document.getElementById('info_calculo');

                        // Obtener el monto en USD desde el atributo data-monto de la opción seleccionada
                        const optionSeleccionada = select.options[select.selectedIndex];
                        const montoUSD = optionSeleccionada.getAttribute('data-monto');

                        if (montoUSD) {
                            const totalBs = parseFloat(montoUSD) * factorDia;

                            // Asignamos el valor numérico al input oculto para el POST de PHP
                            inputReal.value = totalBs.toFixed(2);

                            // Formateamos para el usuario: 19.131,50
                            inputVisible.value = new Intl.NumberFormat('de-DE', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(totalBs);

                        } else {
                            inputReal.value = "";
                            inputVisible.value = "";
                            infoCalculo.innerText = "";
                        }
                    }
                </script>
            </div>
        </div>
    </div>
</body>

</html>