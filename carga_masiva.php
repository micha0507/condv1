<?php
include './modelo/conexion.php';

session_start();
if (empty($_SESSION['id_admin'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('../login.php');
    </script>";
    exit;
}

// Obtener el último factor de la base de datos
$sql_factor = "SELECT factor FROM factor ORDER BY id DESC LIMIT 1";
$resultado_factor = $conexion->query($sql_factor);

if ($resultado_factor && $resultado_factor->num_rows > 0) {
    $fila_factor = $resultado_factor->fetch_assoc();
    $ultimo_factor = $fila_factor['factor'];
} else {
    $ultimo_factor = 1; // Valor predeterminado
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/carga_masiva.css">
    <title>Carga Masiva de Pagos</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <style>
        .rif-search { position: relative; }
        .rif-results { position: absolute; background: white; border: 1px solid #ccc; z-index: 100; width: 100%; }
        .rif-result-item { padding: 8px; cursor: pointer; }
        .rif-result-item:hover { background-color: #f0f0f0; }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="principal">
        <div class="carga_pago">
            <h1>Carga Masiva de Pagos</h1>
            <p><strong>Factor actual:</strong> <?php echo $ultimo_factor; ?></p>
            <form id="pagos-form" method="POST">
                <table id="pagos-table">
                    <thead>
                        <tr>
                            <th>Buscar Propietario</th>
                            <th>Residencia</th>
                            <th>Factura Afectada</th>
                            <th>Monto Bs (Monto * Factor)</th>
                            <th>Referencia</th>
                            <th>Fecha</th>
                            <th>Status</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pagos-container">
                        <tr>
                            <td>
                                <input type="text" name="pagos[0][rif]" class="rif-search" placeholder="Buscar" required>
                                <div class="rif-results"></div>
                            </td>
                            <td>
                                <select name="pagos[0][residencia]" class="residencia-select" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </td>
                            <td>
                                <select name="pagos[0][factura_afectada]" class="factura-select" required>
                                    <option value="">Seleccione Residencia primero</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="pagos[0][monto]" 
                                       class="monto-input" 
                                       data-factor="<?php echo $ultimo_factor; ?>" 
                                       readonly style="background-color: #eee;" required>
                            </td>
                            <td><input type="text" name="pagos[0][referencia]" required></td>
                            <td><input type="date" name="pagos[0][fecha]" class="fecha-input" required></td>
                            <td><input type="checkbox" name="pagos[0][status]"></td>
                            <td><button type="button" class="delete-row">Eliminar</button></td>
                        </tr>
                    </tbody>
                </table>
                <div class="buttons">
                    <button type="button" id="add-pago">Agregar Pago</button>
                    <button type="submit" id="submit-pagos">Cargar Pagos</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        // Guardamos el factor en una constante de JS para usarla globalmente
        const FACTOR_SISTEMA = <?php echo $ultimo_factor; ?>;

        function initializeRifSearch(input) {
            const row = input.closest('tr');
            const resultsContainer = input.nextElementSibling;
            const selectResidencia = row.querySelector('.residencia-select');
            const selectFactura = row.querySelector('.factura-select');
            const inputMonto = row.querySelector('.monto-input');

            let residenciasData = [];

            input.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length > 2) {
                    fetch(`modelo/busca_propietario.php?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            data.forEach(propietario => {
                                const div = document.createElement('div');
                                div.className = 'rif-result-item';
                                div.textContent = `${propietario.rif} - ${propietario.nombre} ${propietario.apellido}`;

                                div.addEventListener('click', () => {
                                    this.value = propietario.rif;
                                    resultsContainer.innerHTML = '';
                                    residenciasData = propietario.residencias;

                                    selectResidencia.innerHTML = '<option value="">Seleccione...</option>';
                                    selectFactura.innerHTML = '<option value="">Seleccione Residencia...</option>';
                                    inputMonto.value = '';

                                    residenciasData.forEach(res => {
                                        const opt = document.createElement('option');
                                        opt.value = res.id;
                                        opt.textContent = res.nro;
                                        selectResidencia.appendChild(opt);
                                    });
                                });
                                resultsContainer.appendChild(div);
                            });
                        });
                }
            });

            selectResidencia.addEventListener('change', function() {
                const residenciaId = this.value;
                selectFactura.innerHTML = '<option value="">Seleccione Factura...</option>';
                inputMonto.value = '';

                if (residenciaId) {
                    const residenciaSeleccionada = residenciasData.find(r => r.id == residenciaId);
                    if (residenciaSeleccionada && residenciaSeleccionada.facturas.length > 0) {
                        residenciaSeleccionada.facturas.forEach(factura => {
                            const opt = document.createElement('option');
                            opt.value = factura.id_factura;
                            opt.textContent = factura.id_factura;
                            opt.dataset.monto = factura.monto; // Monto base de la DB
                            selectFactura.appendChild(opt);
                        });
                    } else if (residenciaId !== "") {
                        selectFactura.innerHTML = '<option value="">Sin facturas pendientes</option>';
                    }
                }
            });

            // AQUÍ SE REALIZA LA MULTIPLICACIÓN AL SELECCIONAR LA FACTURA
            selectFactura.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.monto) {
                    const montoBase = parseFloat(selectedOption.dataset.monto);
                    // Realizamos la operación: Monto * Factor
                    const resultado = montoBase * FACTOR_SISTEMA;
                    inputMonto.value = resultado.toFixed(2);
                } else {
                    inputMonto.value = '';
                }
            });
        }

        function deleteRow(event) {
            const row = event.target.closest('tr');
            row.remove();
        }

        document.querySelectorAll('.rif-search').forEach(input => initializeRifSearch(input));

        let pagoIndex = 1;
        document.getElementById('add-pago').addEventListener('click', () => {
            const container = document.getElementById('pagos-container');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td>
                <input type="text" name="pagos[${pagoIndex}][rif]" class="rif-search" placeholder="Buscar RIF..." required>
                <div class="rif-results"></div>
            </td>
            <td>
                <select name="pagos[${pagoIndex}][residencia]" class="residencia-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </td>
            <td>
                <select name="pagos[${pagoIndex}][factura_afectada]" class="factura-select" required>
                    <option value="">Seleccione Residencia...</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" name="pagos[${pagoIndex}][monto]" 
                       class="monto-input" 
                       data-factor="${FACTOR_SISTEMA}" 
                       readonly style="background-color: #eee;" required>
            </td>
            <td><input type="text" name="pagos[${pagoIndex}][referencia]" required></td>
            <td><input type="date" name="pagos[${pagoIndex}][fecha]" class="fecha-input" required></td>
            <td><input type="checkbox" name="pagos[${pagoIndex}][status]"></td>
            <td><button type="button" class="delete-row">Eliminar</button></td>
        `;
            container.appendChild(newRow);
            initializeRifSearch(newRow.querySelector('.rif-search'));
            newRow.querySelector('.delete-row').addEventListener('click', deleteRow);
            pagoIndex++;
        });

        document.querySelectorAll('.delete-row').forEach(button => {
            button.addEventListener('click', deleteRow);
        });
    </script>
</body>
</html>