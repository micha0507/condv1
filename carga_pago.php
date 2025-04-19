<?php
include './modelo/conexion.php';

// Consulta para obtener el último registro de la tabla 'factor'
$sql_factor = "SELECT factor FROM factor ORDER BY id DESC LIMIT 1";
$resultado_factor = $conexion->query($sql_factor);

if ($resultado_factor && $resultado_factor->num_rows > 0) {
    $fila_factor = $resultado_factor->fetch_assoc();
    $ultimo_factor = $fila_factor['factor'];
} else {
    $ultimo_factor = 1; // Valor predeterminado en caso de que no haya registros
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="./css/pagos.css"> <!-- Verifica que esta ruta sea correcta -->
    <meta charset="UTF-8">
    <title>Carga de Pago</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
<body>

    <!-- barra de navegación -->
    <?php include 'navbar.php'; ?>
    
    <!-- PRINCIPAL -->
    <section class="principal">
        <h1>Carga del Pago:</h1>
        <!-- SECCION BUSQUEDA DE Propietario -->
        <section id="buscarPropietarioForm">  
            <form method="POST" id="searchForm">
            <label for="rif_cedula">RIF/Cédula del Propietario:</label>
            <input type="text" id="rif_cedula" name="rif_cedula" required>
            <button type="submit">Buscar</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['rif_cedula'])) {
            $rif_cedula = $conexion->real_escape_string($_POST['rif_cedula']);
            $query = "SELECT id, nombre, apellido FROM propietario WHERE rif = '$rif_cedula'";
            $result = $conexion->query($query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div class='result'>";
                echo "<p><strong>Propietario:</strong> " . htmlspecialchars($row['nombre'] . " " . $row['apellido']) . "</p>";
                echo "<input type='hidden' id='propietario_id' value='" . htmlspecialchars($row['id']) . "'>";
                echo "<br>";
                echo "<br>";
                echo "<h4>Facturas Asociadas:</h4>";
                echo "</div>";

                // Consulta de facturas asociadas al propietario con INNER JOIN a residencias
                $propietario_id = $row['id'];
                $query_facturas = "
                SELECT f.id_factura, f.fecha_emision, f.fecha_vencimiento, f.periodo, f.monto, r.nro AS nro_residencia, r.id AS residencia_id, f.status 
                FROM facturas f 
                INNER JOIN residencias r ON f.id_residencia = r.id 
                WHERE f.propietario_id = $propietario_id";
                $result_facturas = $conexion->query($query_facturas);

                if ($result_facturas && $result_facturas->num_rows > 0) {
                echo "<table class='tabla_facturas' id='tabla_facturas'>"; // Asegúrate de que esta clase coincida con el CSS
                echo "<thead>";
                echo "<tr>";
                echo "<th>Nro. Factura</th>";
                echo "<th>Fecha Emisión</th>";
                echo "<th>Fecha Vencimiento</th>";
                echo "<th>Periodo</th>";
                echo "<th>Monto</th>";
                echo "<th>Nro. Residencia</th>";
                echo "<th>Estado</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                while ($factura = $result_facturas->fetch_assoc()) {
                    echo "<tr data-residencia-id='" . htmlspecialchars($factura['residencia_id']) . "'>";
                    echo "<td>" . htmlspecialchars($factura['id_factura']) . "</td>";
                    echo "<td>" . htmlspecialchars($factura['fecha_emision']) . "</td>";
                    echo "<td>" . htmlspecialchars($factura['fecha_vencimiento']) . "</td>";
                    echo "<td>" . htmlspecialchars($factura['periodo']) . "</td>";
                    echo "<td>" . htmlspecialchars($factura['monto']) . "</td>";
                    echo "<td>" . htmlspecialchars($factura['nro_residencia']) . "</td>";
                    echo "<td>" . htmlspecialchars($factura['status']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                } else {
                echo "<p>No se encontraron facturas asociadas a este propietario.</p>";
                }
            } else {
                echo "<p>No se encontró ningún propietario con ese RIF/Cédula.</p>";
            }
            }
            ?>
        </section> 
        <script>
            // Limpiar todo después de enviar el formulario
            $(document).ajaxStop(function() {
            $('#searchForm')[0].reset();
            $('#tabla_facturas tbody').empty();
            $('.result').remove();
            $('#monto').val('');
            });
        </script>

        <!-- SECCION PAGO -->
        <div class="carga_pago">
            <form method="POST" action="./modelo/procesar_pago.php" id="pagoForm">
                <input type="hidden" id="propietario_id" name="propietario_id" value="<?php echo $propietario_id ?? ''; ?>">
                <input type="hidden" id="factura_afectada" name="factura_afectada" value="">
                
                <label for="fecha">Fecha del Pago:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                
                <label for="status">Estado del Pago:</label>
                <select id="status" name="status" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Validado">Validado</option>
                </select>

                <label for="monto">Monto:</label>
                <input type="text" id="monto" name="monto" readonly style="pointer-events: none; background-color: #f0f0f0;">

                <label for="referencia">Referencia:</label>
                <input type="text" id="referencia" name="referencia" required>

                <!-- Botón original oculto -->
                <button type="submit" id="submitPago" style="display: none;">Enviar</button>

                <!-- Nuevo botón para ejecutar el ciclo -->
                <button type="button" id="registrarPago">Registrar Pagos</button>
            </form>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            // Obtener el factor desde PHP
            const factor = <?php echo json_encode($ultimo_factor); ?>;

            // Detectar clic en una fila de la tabla
            $('#tabla_facturas tbody tr').on('click', function() {
                $(this).toggleClass('selected');

                // Calcular el monto total de las filas seleccionadas
                let totalMonto = 0;
                $('#tabla_facturas tbody tr.selected').each(function() {
                    let monto = parseFloat($(this).find('td:nth-child(5)').text());
                    totalMonto += monto * factor;
                });

                // Actualizar el campo de monto visualmente
                $('#monto').val(totalMonto.toFixed(2) + ' Bs');
            });

            // Enviar el formulario para cada factura seleccionada
            $('#registrarPago').on('click', function() {
                let facturasSeleccionadas = [];
                $('#tabla_facturas tbody tr.selected').each(function() {
                    let facturaId = $(this).find('td:first').text(); // Asume que el ID está en la primera columna
                    facturasSeleccionadas.push(facturaId);
                });

                if (facturasSeleccionadas.length === 0) {
                    alert('Por favor, seleccione al menos una factura.');
                    return;
                }

                // Ocultar el botón para evitar múltiples clics
                $('#registrarPago').prop('disabled', true);

                // Ciclo para enviar el formulario una vez por cada factura seleccionada
                let index = 0;

                function enviarFormulario() {
                    if (index < facturasSeleccionadas.length) {
                        let facturaId = facturasSeleccionadas[index];

                        // Asignar el ID de la factura al campo oculto
                        $('#factura_afectada').val(facturaId);

                        // Enviar el formulario mediante AJAX
                        $.ajax({
                            url: $('#pagoForm').attr('action'),
                            method: 'POST',
                            data: $('#pagoForm').serialize(),
                            success: function(response) {
                                console.log('Pago registrado para factura ID:', facturaId);
                                index++;
                                enviarFormulario(); // Llamar recursivamente para la siguiente factura
                            },
                            error: function() {
                                alert('Ocurrió un error al registrar el pago para la factura ID: ' + facturaId);
                                $('#registrarPago').prop('disabled', false);
                            }
                        });
                    } else {
                        alert('Pagos registrados exitosamente.');
                        $('#registrarPago').prop('disabled', false);

                        // Limpiar el formulario después de la carga
                        $('#pagoForm')[0].reset();
                        $('#monto').val('');
                        $('#tabla_facturas tbody tr').removeClass('selected');
                    }
                }

                enviarFormulario(); // Iniciar el ciclo de envío
            });
        });
    </script>
</body>
</html>
