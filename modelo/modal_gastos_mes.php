<!-- Modal -->
<div id="modalGastosMes" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModalGastos()">&times;</span>
        <h2>Gastos del Mes</h2>

        <!-- Formulario para filtrar por mes y año -->
        <form method="GET" action="">
            <input type="hidden" name="origen" value="gastos">
            <label for="mes">Mes:</label>
            <select name="mes" id="mes" required>
                <?php
                // Array de meses en español
                $meses_espanol = [
                    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                ];

                // Generar opciones de meses
                foreach ($meses_espanol as $numero => $nombre) {
                    $selected = (isset($_GET['mes']) && $_GET['mes'] == $numero) ? 'selected' : '';
                    echo "<option value='$numero' $selected>$nombre</option>";
                }
                ?>
            </select>

            <label for="anio">Año:</label>
            <select name="anio" id="anio" required>
                <?php
                // Generar opciones de años (últimos 5 años)
                $anio_actual = date('Y');
                for ($i = $anio_actual; $i >= $anio_actual - 5; $i--) {
                    $selected = (isset($_GET['anio']) && $_GET['anio'] == $i) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>

            <button type="submit">Filtrar</button>
        </form>

        <div class="tabla-gastos">
            <?php
            include 'conexion.php';

            // Obtener mes y año seleccionados o usar el mes y año actual (saneados)
            $mes = isset($_GET['mes']) ? str_pad(preg_replace('/[^0-9]/', '', $_GET['mes']), 2, '0', STR_PAD_LEFT) : date('m');
            $anio = isset($_GET['anio']) ? preg_replace('/[^0-9]/', '', $_GET['anio']) : date('Y');
            if ($mes === '' || (int)$mes < 1 || (int)$mes > 12) {
                $mes = date('m');
            }
            if ($anio === '') {
                $anio = date('Y');
            }

            // Consultar los datos de la tabla gastos_eventuales (con sentencia preparada)
            $sql = "SELECT id_gasto, concepto, categoria, fecha, monto 
                    FROM gastos_eventuales 
                    WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?";
            $stmt_gastos_mes = $conexion->prepare($sql);
            $stmt_gastos_mes->bind_param("ss", $mes, $anio);
            $stmt_gastos_mes->execute();
            $resultado = $stmt_gastos_mes->get_result();

            // Mostrar los datos en una tabla
            echo "<table border='1'>";
            echo "<thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Concepto</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                        <th>Monto (Bs)</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            $total_montos = 0;
            if ($resultado && $resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['id_gasto']}</td>
                            <td>" . htmlspecialchars($fila['concepto']) . "</td>
                            <td>" . htmlspecialchars($fila['categoria']) . "</td>
                            <td>{$fila['fecha']}</td>
                            <td>" . number_format($fila['monto'], 2) . "</td>
                          </tr>";
                    $total_montos += $fila['monto'];
                }
            } else {
                echo "<tr><td colspan='5'>No se encontraron registros para este mes.</td></tr>";
            }
            echo "</tbody>";
            echo "<tfoot>
                    <tr>
                        <td colspan='4'><b>Total:</b></td>
                        <td><b>" . number_format($total_montos, 2) . " Bs</b></td>
                    </tr>
                  </tfoot>";
            echo "</table>";
            $stmt_gastos_mes->close();

            // Enlace para el PDF/reporte con el mismo filtro mes/año aplicado
            $link_pdf_gastos = 'modelo/comprobante_pagosmes.php?tipo=gastos&mes=' . urlencode($mes) . '&anio=' . urlencode($anio);

            // Cerrar la conexión
            $conexion->close();
            ?>
        </div>

        <button type="button" id="btnShowPrintModalGastos" data-pdf-link="<?php echo htmlspecialchars($link_pdf_gastos); ?>" style="display:block; text-align:center; margin-top:15px; background:#e74c3c; padding:10px; border-radius:5px; color:white; text-decoration:none; border:none; width:100%; cursor:pointer; font-size:14px;">
            Guardar PDF / Imprimir
        </button>
    </div>
</div>

<!-- Modal de confirmación de PDF (Gastos del Mes) -->
<div id="modalFondoPrintGastos" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1001;"></div>
<div id="modalPrintGastos" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:25px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.3); z-index:1002; text-align:center; width:350px;">
    <h2 style="margin:0 0 10px 0; color:#4CAF50;">Listado Generado Exitosamente</h2>
    <p style="color:#666; font-size:14px;">¿Deseas emitir el PDF del listado de gastos del mes?</p>
    <div style="margin-top:25px; display:flex; justify-content:center; gap:10px;">
        <button id="confirmarPrintGastos" style="padding:10px 20px; background-color:#1ecaf5; color:white; border:none; border-radius:5px; cursor:pointer;">Imprimir</button>
        <button id="cancelarPrintGastos" style="padding:10px 20px; background-color:#e74c3c; color:white; border:none; border-radius:5px; cursor:pointer;">Cancelar</button>
    </div>
</div>

<!-- Estilos para el modal -->
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-contenido {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 800px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.cerrar {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.cerrar:hover,
.cerrar:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}
</style>

<!-- Script para abrir y cerrar el modal -->
<script>
function abrirModalGastos() {
    document.getElementById('modalGastosMes').style.display = 'block';
}

function cerrarModalGastos() {
    document.getElementById('modalGastosMes').style.display = 'none';
}

// Mantener el modal abierto si se filtró desde ESTE formulario (Gastos)
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('origen') === 'gastos') {
        abrirModalGastos();
    }
});

// --- Modal de confirmación para emitir el PDF de Gastos del Mes ---
document.addEventListener('DOMContentLoaded', function() {
    var btnShowPrint = document.getElementById('btnShowPrintModalGastos');
    var modalFondoPrint = document.getElementById('modalFondoPrintGastos');
    var modalPrint = document.getElementById('modalPrintGastos');
    var btnConfirmar = document.getElementById('confirmarPrintGastos');
    var btnCancelar = document.getElementById('cancelarPrintGastos');

    if (!btnShowPrint || !modalFondoPrint || !modalPrint) {
        return;
    }

    function abrirModalPrint() {
        modalFondoPrint.style.display = 'block';
        modalPrint.style.display = 'block';
    }

    function cerrarModalPrint() {
        modalFondoPrint.style.display = 'none';
        modalPrint.style.display = 'none';
    }

    btnShowPrint.addEventListener('click', abrirModalPrint);
    modalFondoPrint.addEventListener('click', cerrarModalPrint);
    if (btnCancelar) {
        btnCancelar.addEventListener('click', cerrarModalPrint);
    }
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', function() {
            var link = btnShowPrint.getAttribute('data-pdf-link');
            window.open(link, '_blank');
            cerrarModalPrint();
        });
    }
});
</script>