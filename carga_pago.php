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
    <link rel="stylesheet" href="./css/pagos.css">
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
                    echo "<h4>Facturas Asociadas:</h4>";
                    echo "</div>";

                    // Consulta de facturas asociadas al propietario
                    $propietario_id = $row['id'];
                    $query_facturas = "
                    SELECT f.id_factura, f.periodo, f.monto, r.nro AS nro_residencia, r.id AS residencia_id
                    FROM facturas f
                    INNER JOIN residencias r ON f.id_residencia = r.id
                    WHERE f.propietario_id = $propietario_id AND f.status = 'Pendiente'";
                    $result_facturas = $conexion->query($query_facturas);

                    if ($result_facturas && $result_facturas->num_rows > 0) {
                        echo "<table class='tabla_facturas' id='tabla_facturas'>";
                        echo "<thead><tr><th>Nro. Factura</th><th>Periodo</th><th>Monto</th><th>Nro. Residencia</th></tr></thead>";
                        echo "<tbody>";
                        while ($factura = $result_facturas->fetch_assoc()) {
                            $monto_mostrado = $factura['monto'] * $ultimo_factor;
                            echo "<tr data-residencia-id='" . htmlspecialchars($factura['residencia_id']) . "' data-monto-base='" . htmlspecialchars($factura['monto']) . "'>";
                            echo "<td>" . htmlspecialchars($factura['id_factura']) . "</td>";
                            echo "<td>" . htmlspecialchars($factura['periodo']) . "</td>";
                            echo "<td>" . number_format($monto_mostrado, 2) . " Bs</td>";
                            echo "<td>" . htmlspecialchars($factura['nro_residencia']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No se encontraron facturas pendientes asociadas a este propietario.</p>";
                    }
                } else {
                    echo "<p>No se encontró ningún propietario con ese RIF/Cédula.</p>";
                }
            }
            ?>
        </section>

        <!-- SECCION PAGO -->
        <div class="carga_pago">
            <form method="POST" action="./modelo/procesar_pago.php" id="pagoForm">
                <input type="hidden" id="propietario_id" name="propietario_id" value="<?php echo $propietario_id ?? ''; ?>">
                <input type="hidden" id="factura_afectada" name="factura_afectada" value="">
               
                <label for="fecha">Fecha del Pago:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
               
                <label for="status">Estado del Pago:</label>
                <select id="status" name="status" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Validado">Validado</option>
                </select>

                <label for="monto">Monto:</label>
                <input type="text" id="monto" name="monto" readonly style="pointer-events: none; background-color: #f0f0f0;">

                <label for="referencia">Referencia:</label>
                <input type="text" id="referencia" name="referencia" placeholder="Ingrese la referencia del pago" required>

                <button type="button" id="registrarPago">Registrar Pagos</button>
            </form>
        </div>

        <!-- Fondo oscuro para el modal -->
        <div id="modalFondo" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 999;"></div>

        <!-- Modal para confirmar el registro del pago -->
        <div id="modalExito" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(255, 255, 255, 1); z-index: 1000; padding: 20px; border-radius: 8px; width: 300px; text-align: center; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #4CAF50;">¡Pago registrado exitosamente!</h2>
            <p>El pago se ha registrado correctamente.</p>
            <button id="btnImprimir" style="margin: 10px; padding: 10px 20px; background-color: #1ecaf5; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir</button>
            <button id="btnCerrarModal" style="margin: 10px; padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">Cerrar</button>
        </div>

        <script>
        $(document).ready(function() {
            const factor = <?php echo json_encode($ultimo_factor); ?>;

            // Detectar clic en una fila de la tabla
            $('#tabla_facturas tbody').on('click', 'tr', function() {
                $(this).toggleClass('selected');

                // Calcular el monto total
                let totalMonto = 0;
                $('#tabla_facturas tbody tr.selected').each(function() {
                    const montoBase = parseFloat($(this).data('monto-base'));
                    if (!isNaN(montoBase)) {
                        totalMonto += montoBase * factor;
                    }
                });

                // Actualizar el campo de monto
                $('#monto').val(totalMonto.toFixed(2) + ' Bs');
            });

            // Registrar pagos
            $('#registrarPago').on('click', function() {
                const facturasSeleccionadas = [];

                // Recopilar las facturas seleccionadas
                $('#tabla_facturas tbody tr.selected').each(function() {
                    const facturaId = $(this).find('td:first').text();
                    const montoBase = parseFloat($(this).data('monto-base'));
                    if (!isNaN(montoBase)) {
                        facturasSeleccionadas.push({
                            id: facturaId,
                            monto: (montoBase * factor).toFixed(2)
                        });
                    }
                });

                if (facturasSeleccionadas.length === 0) {
                    alert('Por favor, seleccione al menos una factura.');
                    return;
                }

                // Enviar cada factura seleccionada
                facturasSeleccionadas.forEach(factura => {
                    const formData = new FormData();
                    formData.append('propietario_id', $('#propietario_id').val());
                    formData.append('factura_afectada', factura.id);
                    formData.append('fecha', $('#fecha').val());
                    formData.append('status', $('#status').val());
                    formData.append('monto', factura.monto);
                    formData.append('referencia', $('#referencia').val());

                    $.ajax({
                        url: $('#pagoForm').attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log('Pago registrado para factura ID:', factura.id);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error al registrar el pago para factura ID:', factura.id);
                        }
                    });
                });

                // Mostrar el modal de éxito y el fondo oscuro
                $('#modalFondo').fadeIn();
                $('#modalExito').fadeIn();

                // Limpiar el formulario y la tabla
                limpiarFormulario();
            });

            // Función para limpiar el formulario y la tabla
            function limpiarFormulario() {
                $('#pagoForm')[0].reset(); // Restablecer el formulario
                $('#tabla_facturas tbody tr').removeClass('selected'); // Quitar la selección de las filas
                $('#monto').val(''); // Limpiar el campo de monto
            }

            // Botón para cerrar el modal
            $('#btnCerrarModal').on('click', function() {
                $('#modalFondo').fadeOut();
                $('#modalExito').fadeOut();
            });

            // Botón para imprimir el comprobante
            $('#btnImprimir').on('click', function() {
                window.open('./modelo/comprobante_pago.php', '_blank'); // Abrir en una nueva pestaña
            });
        });
        </script>
    </section>
</body>
</html>