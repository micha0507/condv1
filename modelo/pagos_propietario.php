<?php
// Ajusta la ruta de conexión si es necesario (ej: '../modelo/conexion.php')
include 'conexion.php'; 
session_start();

if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
$id_propietario = $_SESSION['id'];

// --- PROCESADOR DE PAGO (LÓGICA DEL SERVIDOR) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_pago'])) {
    $facturas = json_decode($_POST['facturas'], true);
    $referencia = trim($_POST['referencia']);
    $fecha_pago = $_POST['fecha'];
    
    $exito = true;
    $errores = [];

    foreach ($facturas as $factura) {
        $id_factura = intval($factura['id']);
        $monto_usd = floatval($factura['monto']);
        
        // IMPORTANTE: Verifica que estos nombres de columna existan en tu tabla 'pagos'
        $sql = "INSERT INTO pagos (id_propietario, id_factura, monto, referencia, fecha, status) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        
        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iids s", $id_propietario, $id_factura, $monto_usd, $referencia, $fecha_pago);
            if (!$stmt->execute()) {
                $exito = false;
                $errores[] = $stmt->error;
            }
        } else {
            $exito = false;
            $errores[] = $conexion->error;
        }
    }

    if ($exito) {
        echo "success";
    } else {
        echo "Error: " . implode(" | ", $errores);
    }
    exit; 
}

// --- CONSULTA DE DATOS PARA LA VISTA ---
$sql_f = "SELECT factor FROM factor ORDER BY id DESC LIMIT 1";
$res_f = $conexion->query($sql_f);
$ultimo_factor = ($res_f && $res_f->num_rows > 0) ? $res_f->fetch_assoc()['factor'] : 1;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de Pago | Condominio</title>
    <link rel="stylesheet" href="./css/pagos.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root { --verde: #27ae60; --oscuro: #2c3e50; }
        .selected { background-color: #d1e7dd !important; border: 2px solid var(--verde) !important; }
        .tabla_facturas tbody tr { cursor: pointer; transition: all 0.2s; }
        .tabla_facturas tbody tr:hover { background-color: #f8f9fa; }
        #modalFondo { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; }
        .modal { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:30px; border-radius:15px; z-index:1001; text-align:center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <?php include '../navbar_propietario.php'; ?>

    <section class="principal">
        <div class="encabezado_principal">
            <h1>Carga de Pago</h1>
            <p>Haga clic en las facturas que desea pagar y complete los datos de la derecha.</p>
        </div>

        <div class="contenedor_pago">
            <div class="cuadro_listado">
                <h3>Facturas por Pagar</h3>
                <table id="tabla_facturas" class="tabla_facturas">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Vence</th>
                            <th>Monto ($)</th>
                            <th>Monto (Bs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conexion->prepare("SELECT id_factura, fecha_vencimiento, monto FROM facturas WHERE propietario_id = ? AND status = 'Pendiente'");
                        $stmt->bind_param("i", $id_propietario);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while ($f = $res->fetch_assoc()):
                            $monto_bs = $f['monto'] * $ultimo_factor;
                        ?>
                        <tr data-id="<?= $f['id_factura'] ?>" data-monto="<?= $f['monto'] ?>">
                            <td>#<?= $f['id_factura'] ?></td>
                            <td><?= date("d/m/Y", strtotime($f['fecha_vencimiento'])) ?></td>
                            <td><?= number_format($f['monto'], 2) ?> $</td>
                            <td><?= number_format($monto_bs, 2) ?> Bs</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="cuadro_carga">
                <form id="pagoForm">
                    <div class="campo">
                        <label>Total Seleccionado ($)</label>
                        <input type="text" id="total_usd" value="0.00" readonly>
                    </div>
                    <div class="campo">
                        <label>Total en Bolívares (Tasa: <?= $ultimo_factor ?>)</label>
                        <input type="text" id="total_bs" value="0.00" readonly style="color: var(--verde); font-weight: bold;">
                    </div>
                    <div class="campo">
                        <label>Referencia Bancaria</label>
                        <input type="text" id="referencia" placeholder="Ej: 12345678" required>
                    </div>
                    <div class="campo">
                        <label>Fecha de Transferencia</label>
                        <input type="date" id="fecha_pago" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="button" id="btnNotificar" class="btn_enviar">Registrar Pago</button>
                </form>
            </div>
        </div>
    </section>

    <div id="modalFondo"></div>
    <div id="modalExito" class="modal">
        <span class="material-symbols-outlined" style="font-size: 64px; color: var(--verde);">check_circle</span>
        <h2>¡Reporte Enviado!</h2>
        <p>Tu pago ha sido registrado. Espera la validación de administración.</p>
        <button id="btnCerrarModal" style="margin-top:20px; padding:10px 30px; background:var(--oscuro); color:white; border:none; border-radius:5px; cursor:pointer;">Aceptar</button>
    </div>

    <script>
    $(document).ready(function() {
        let factor = <?= $ultimo_factor ?>;
        let facturasSeleccionadas = [];

        // Selección de filas
        $('#tabla_facturas tbody').on('click', 'tr', function() {
            $(this).toggleClass('selected');
            actualizarTotales();
        });

        function actualizarTotales() {
            let totalUSD = 0;
            facturasSeleccionadas = [];
            $('#tabla_facturas tbody tr.selected').each(function() {
                let id = $(this).data('id');
                let monto = parseFloat($(this).data('monto'));
                totalUSD += monto;
                facturasSeleccionadas.push({ id: id, monto: monto });
            });
            $('#total_usd').val(totalUSD.toFixed(2));
            $('#total_bs').val((totalUSD * factor).toFixed(2));
        }

        $('#btnNotificar').on('click', function() {
            const btn = $(this);
            const ref = $('#referencia').val();
            
            if (facturasSeleccionadas.length === 0) return alert("Selecciona al menos una factura.");
            if (!ref) return alert("Escribe el número de referencia.");

            btn.prop('disabled', true).text('Procesando...');

            $.ajax({
                url: window.location.href,
                method: 'POST',
                data: {
                    ajax_pago: true,
                    facturas: JSON.stringify(facturasSeleccionadas),
                    referencia: ref,
                    fecha: $('#fecha_pago').val(),
                    monto_total: $('#total_bs').val()
                },
                success: function(resp) {
                    if(resp.trim() === "success") {
                        $('#modalFondo').fadeIn();
                        $('#modalExito').fadeIn();
                        $('.selected').remove();
                        $('#pagoForm')[0].reset();
                        actualizarTotales();
                    } else {
                        alert("Error del servidor: " + resp);
                    }
                },
                error: function() {
                    alert("Error de red. Verifica tu conexión.");
                },
                complete: function() {
                    btn.prop('disabled', false).text('Registrar Pago');
                }
            });
        });

        $('#btnCerrarModal').click(function() {
            $('#modalFondo, #modalExito').fadeOut();
        });
    });
    </script>
</body>
</html>