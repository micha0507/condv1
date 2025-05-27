<!-- Modal -->
<div id="modalPagosMes" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModalPagos()">&times;</span>
        <h2>Pagos del Mes</h2>

        <!-- Formulario para filtrar por mes y año -->
        <form method="GET" action="">
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

        <div class="tabla-pagos">
            <?php
            include 'conexion.php';

            // Obtener mes y año seleccionados o usar el mes y año actual
            $mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
            $anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

            // Consultar los datos de la tabla pagos
            $sql = "SELECT id, fecha_registro, monto, referencia 
                    FROM pagos 
                    WHERE MONTH(fecha_registro) = '$mes' AND YEAR(fecha_registro) = '$anio'";
            $resultado = $conexion->query($sql);

            // Mostrar los datos en una tabla
            echo "<table border='1'>";
            echo "<thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha de Registro</th>
                        <th>Monto (Bs)</th>
                        <th>Referencia</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            $total_montos = 0;
            if ($resultado && $resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['id']}</td>
                            <td>{$fila['fecha_registro']}</td>
                            <td>" . number_format($fila['monto'], 2) . "</td>
                            <td>{$fila['referencia']}</td>
                          </tr>";
                    $total_montos += $fila['monto'];
                }
            } else {
                echo "<tr><td colspan='4'>No se encontraron registros para este mes.</td></tr>";
            }
            echo "</tbody>";
            echo "<tfoot>
                    <tr>
                        <td colspan='2'><b>Total:</b></td>
                        <td colspan='2'><b>" . number_format($total_montos, 2) . " Bs</b></td>
                    </tr>
                  </tfoot>";
            echo "</table>";

            // Cerrar la conexión
            $conexion->close();
            ?>
        </div>
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
function abrirModalPagos() {
    document.getElementById('modalPagosMes').style.display = 'block';
}

function cerrarModalPagos() {
    document.getElementById('modalPagosMes').style.display = 'none';
}

// Mantener el modal abierto si hay parámetros GET en la URL
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('mes') || urlParams.has('anio')) {
        abrirModalPagos();
    }
};
</script>