<?php
include './modelo/conexion.php';
session_start();
if (empty($_SESSION['id_admin'])) {
    header("Location: ../login.php");
    exit;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Pagos</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <link rel="stylesheet" href="./css/tabla_pagos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Contenedor relativo para el input */
        /* Esto asegura que la celda no corte el contenido */
        td {
            overflow: visible !important;
        }

        .results-list {
            position: absolute;
            /* Forzamos que aparezca justo debajo del input */
            top: 100%;
            left: 0;
            width: 100%;
            min-width: 250px;
            /* Evita que sea muy estrecha */
            background: white;
            border: 1px solid #87898a;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

            /* Z-index extremadamente alto */
            z-index: 999999 !important;

            max-height: 200px;
            overflow-y: auto;
            display: none;
            /* Se activa por JS */
        }

        /* Estilo para los ítems de la lista */
        .result-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            background: #fff;
        }

        .result-item:hover {
            background: #f1f7ff;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="principal">
        <h1>Carga Masiva de Pagos</h1>

        <form action="modelo/procesar_carga_total.php" method="POST">


            <table>
                <thead>
                    <tr>
                        <th>Buscar Propietario (RIF/Nombre)</th>
                        <th>Residencia</th>
                        <th>Factura Afectada</th>
                        <th>Monto (Bs)</th>
                        <th>Referencia</th>
                        <th>Fecha</th>
                        <th>Validar</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="container">
                </tbody>
            </table> <br>
            <div style="margin-bottom: 20px;"><br><br>
                <button type="button" id="addRow" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
                    Añadir Fila
                </button>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px;">
                    Procesar Carga Total
                </button>
            </div>
        </form>
    </div>

    <script>
        // 1. Variables globales
        let pagoIndex = 0;
        // Aseguramos que si PHP no devuelve nada, JS reciba un número válido
        const TASA_SISTEMA = <?php echo json_encode((float)$tasa); ?>;
        const MENSUALIDAD_SISTEMA = <?php echo json_encode((float)$mensualidad_usd); ?>;

        // 2. Función para formatear visualmente (12.500,00)
        function formatearMonto(valor) {
            return new Intl.NumberFormat('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(valor);
        }

        // 3. Función para crear la fila
        function createRow() {
            const container = document.getElementById('container');
            const tr = document.createElement('tr');
            const index = pagoIndex;

            // Calculamos el monto basado en la base de datos
            const montoCalculado = formatearMonto(TASA_SISTEMA * MENSUALIDAD_SISTEMA);

            tr.innerHTML = `
            <td style="position:relative; min-width: 180px;">
                    <input type="text" class="rif-search" placeholder="Nombre o RIF..." autocomplete="off" style="width: 100%;">
                    <div class="results-list" style="display:none;"></div>
                    <input type="hidden" name="pagos[${index}][id_propietario]" class="id-propietario">
            </td>
            <td>
                <select name="pagos[${index}][residencia]" class="residencia-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </td>
            <td>
                <select name="pagos[${index}][factura_afectada]" class="factura-select" required>
                    <option value="">Seleccione Propietario...</option>
                </select>
            </td>
            <td>
                <input type="text" name="pagos[${index}][monto]" value="${montoCalculado}" class="monto-input" readonly 
                       style="text-align: right; font-weight: bold; background: #f4f4f4;">
            </td>
            <td><input type="text" name="pagos[${index}][referencia]" required placeholder="Nro de Transferencia"></td>
            <td><input type="date" name="pagos[${index}][fecha]" value="<?php echo date('Y-m-d'); ?>" required></td>
            <td style="text-align:center;"><input type="checkbox" name="pagos[${index}][status]" value="Validado" checked></td>
            <td><button type="button" class="btn-del" style="background:#e74c3c; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">Eliminar</button></td>
        `;

            container.appendChild(tr);
            setupRowEvents(tr);
            pagoIndex++;
        }

        // 4. Lógica de búsqueda y eventos por fila
        function setupRowEvents(tr) {
            const input = tr.querySelector('.rif-search');
            const results = tr.querySelector('.results-list');
            const resSelect = tr.querySelector('.residencia-select');
            const facSelect = tr.querySelector('.factura-select');
            const idInput = tr.querySelector('.id-propietario');

            input.addEventListener('input', function() {
                if (this.value.length < 2) {
                    results.style.display = 'none';
                    return;
                }

                $.get('modelo/busca_propietario.php', {
                    query: this.value
                }, function(data) {
                    results.innerHTML = '';
                    if (data && data.length > 0) {
                        results.style.display = 'block';
                        data.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'result-item';
                            div.innerText = `${p.rif} - ${p.nombre} ${p.apellido}`;
                            div.onclick = () => {
                                input.value = `${p.nombre} ${p.apellido}`;
                                idInput.value = p.id;
                                results.style.display = 'none';
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
                        opt.text = `ID: ${f.id_factura} (Deuda: ${f.monto} Bs)`;
                        facSelect.appendChild(opt);
                    });
                }
            };

            tr.querySelector('.btn-del').onclick = () => {
                if (document.querySelectorAll('#container tr').length > 1) {
                    tr.remove();
                } else {
                    alert("Debe haber al menos una fila.");
                }
            };
        }

        function populateResidencias(select, residencias) {
            select.innerHTML = '<option value="">Seleccione Residencia...</option>';
            residencias.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.text = `Residencia Nro: ${r.nro}`;
                $(opt).data('facturas', r.facturas);
                select.appendChild(opt);
            });
        }

        // 5. Inicialización
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('addRow').onclick = createRow;
            createRow(); // Crea la primera fila automáticamente
        });
    </script>
</body>

</html>