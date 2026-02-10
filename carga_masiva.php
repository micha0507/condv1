<?php
include './modelo/conexion.php';
session_start();
if (empty($_SESSION['id_admin'])) { header("Location: ../login.php"); exit; }

// 4. Obtener último factor y monto_mensual para el cálculo automático
$sql_f = "SELECT factor, monto_mensual FROM factor ORDER BY id DESC LIMIT 1";
$res_f = $conexion->query($sql_f)->fetch_assoc();
$tasa = $res_f['factor'] ?? 0;
$mensualidad_usd = $res_f['monto_mensual'] ?? 0;
$monto_bs_final = number_format($tasa * $mensualidad_usd, 2, '.', ''); // Operación matemática
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carga Masiva de Pagos</title>
    <link rel="stylesheet" href="./css/carga_masiva.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .results-list { position: absolute; background: white; border: 1px solid #ddd; z-index: 1000; width: 100%; max-height: 150px; overflow-y: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .result-item { padding: 8px; cursor: pointer; border-bottom: 1px solid #eee; }
        .result-item:hover { background: #f0f0f0; }
        input[readonly] { background-color: #f4f4f4; color: #555; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div style="padding: 20px;">
        <h1>Registro Masivo de Pagos</h1>
        <button type="button" id="addRow" style="margin-bottom: 10px; padding: 10px; background: #1ecaf5; color: white; border: none; border-radius: 4px; cursor: pointer;">+ Añadir Fila</button>

        <form action="modelo/procesar_carga_total.php" method="POST">
            <table id="tablaPagos" border="1" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th>Buscar Propietario</th>
                        <th>Residencia</th>
                        <th>Factura Afectada</th>
                        <th>Monto Bs</th>
                        <th>Referencia</th>
                        <th>Fecha</th>
                        <th>Status (Validado)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="container">
                    </tbody>
            </table>
            <button type="submit" style="margin-top: 20px; padding: 12px 25px; background: #27ae60; color: white; border: none; cursor: pointer; border-radius: 4px; font-weight: bold;">Cargar Pagos</button>
        </form>
    </div>

    <script>
        let rowCount = 0;
        const MONTO_CALCULADO = "<?php echo $monto_bs_final; ?>";

        function createRow() {
            const index = rowCount;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="position:relative">
                    <input type="text" class="search-prop" placeholder="Nombre o Cédula..." required autocomplete="off" style="width:90%">
                    <input type="hidden" name="pagos[${index}][id_propietario]" class="id-propietario">
                    <div class="results-list" style="display:none"></div>
                </td>
                <td>
                    <select class="residencia-select" required style="width:100%"><option value="">Seleccione...</option></select>
                </td>
                <td>
                    <select name="pagos[${index}][factura_afectada]" class="factura-select" required style="width:100%"><option value="">--</option></select>
                </td>
                <td>
                    <input type="number" name="pagos[${index}][monto]" value="${MONTO_CALCULADO}" readonly style="width:90%">
                </td>
                <td><input type="text" name="pagos[${index}][referencia]" required style="width:90%"></td>
                <td><input type="date" name="pagos[${index}][fecha_registro]" required style="width:90%"></td>
                <td style="text-align:center">
                    <input type="checkbox" name="pagos[${index}][status]" value="Validado">
                </td>
                <td>${index > 0 ? '<button type="button" class="btn-del" style="color:red; cursor:pointer;">Eliminar</button>' : '-'}</td>
            `;
            document.getElementById('container').appendChild(tr);
            setupRowLogic(tr);
            rowCount++;
        }

        function setupRowLogic(tr) {
            const input = tr.querySelector('.search-prop');
            const results = tr.querySelector('.results-list');
            const resSelect = tr.querySelector('.residencia-select');
            const facSelect = tr.querySelector('.factura-select');
            const idInput = tr.querySelector('.id-propietario');

            input.addEventListener('input', function() {
                if (this.value.length < 2) { results.style.display = 'none'; return; }
                $.get('modelo/busca_propietario.php', { query: this.value }, function(data) {
                    results.innerHTML = '';
                    if (data.length > 0) {
                        results.style.display = 'block';
                        data.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'result-item';
                            div.innerText = `${p.rif} - ${p.nombre} ${p.apellido}`;
                            div.onclick = () => {
                                input.value = `${p.nombre} ${p.apellido}`;
                                idInput.value = p.id;
                                results.style.display = 'none';
                                // Cargar residencias del propietario
                                populateResidencias(resSelect, p.residencias);
                            };
                            results.appendChild(div);
                        });
                    }
                });
            });

            resSelect.onchange = function() {
                const selectedOption = this.options[this.selectedIndex];
                const facturas = $(selectedOption).data('facturas');
                facSelect.innerHTML = '<option value="">Seleccione Factura...</option>';
                if (facturas) {
                    facturas.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id_factura;
                        opt.text = `ID: ${f.id_factura} (Deuda: ${f.monto} $)`;
                        facSelect.appendChild(opt);
                    });
                }
            };

            if (tr.querySelector('.btn-del')) {
                tr.querySelector('.btn-del').onclick = () => tr.remove();
            }
        }

        function populateResidencias(select, residencias) {
            select.innerHTML = '<option value="">Seleccione Residencia...</option>';
            residencias.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.text = `Nro: ${r.nro}`;
                $(opt).data('facturas', r.facturas);
                select.appendChild(opt);
            });
        }

        document.getElementById('addRow').onclick = createRow;
        window.onload = createRow;
    </script>
</body>
</html>