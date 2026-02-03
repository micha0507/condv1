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
        .rif-search {
            position: relative;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="principal">
        <div class="carga_pago">
            <h1>Carga Masiva de Pagos</h1>
            <form id="pagos-form" method="POST">
                <table id="pagos-table">
                    <thead>
                        <tr>
                            <th>RIF del Propietario</th>
                            <th>Residencia</th>
                            <th>Factura Afectada</th>
                            <th>Monto</th>
                            <th>Referencia</th>
                            <th>Fecha</th>
                            <th>Status</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pagos-container">
                        <tr>
                            <td>
                                <input type="text" name="pagos[0][rif]" class="rif-search" placeholder="Buscar RIF..." required>
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
                                <input type="number" step="0.01" name="pagos[0][monto]" class="monto-input" readonly style="background-color: #eee;" required>
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
        function initializeRifSearch(input) {
            const row = input.closest('tr');
            const resultsContainer = input.nextElementSibling;
            const selectResidencia = row.querySelector('.residencia-select');
            const selectFactura = row.querySelector('.factura-select');
            const inputMonto = row.querySelector('.monto-input');

            // Objeto para guardar las residencias y facturas del propietario actual
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

                                    // Guardamos la data de residencias con sus facturas
                                    residenciasData = propietario.residencias;

                                    // Llenar select de Residencias
                                    selectResidencia.innerHTML = '<option value="">Seleccione...</option>';
                                    selectFactura.innerHTML = '<option value="">Seleccione Residencia...</option>';
                                    inputMonto.value = '';

                                    residenciasData.forEach(res => {
                                        const opt = document.createElement('option');
                                        opt.value = res.id; // Usamos el ID de la residencia
                                        opt.textContent = res.nro;
                                        selectResidencia.appendChild(opt);
                                    });
                                });
                                resultsContainer.appendChild(div);
                            });
                        });
                }
            });

            // Evento 1: Al cambiar Residencia, llenar Facturas
            selectResidencia.addEventListener('change', function() {
                const residenciaId = this.value;
                selectFactura.innerHTML = '<option value="">Seleccione Factura...</option>';
                inputMonto.value = '';

                if (residenciaId) {
                    // Buscar la residencia seleccionada en los datos
                    const residenciaSeleccionada = residenciasData.find(r => r.id == residenciaId);

                    if (residenciaSeleccionada && residenciaSeleccionada.facturas.length > 0) {
                        residenciaSeleccionada.facturas.forEach(factura => {
                            const opt = document.createElement('option');
                            opt.value = factura.id_factura;
                            opt.textContent = factura.id_factura;
                            opt.dataset.monto = factura.monto; // Guardamos el monto en el dataset
                            selectFactura.appendChild(opt);
                        });
                    } else if (residenciaId !== "") {
                        selectFactura.innerHTML = '<option value="">Sin facturas pendientes</option>';
                    }
                }
            });

            // Evento 2: Al cambiar Factura, precargar Monto
            selectFactura.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.monto) {
                    // Aquí se refleja el monto que trajimos de la tabla pagos en el paso anterior
                    inputMonto.value = selectedOption.dataset.monto;
                } else {
                    inputMonto.value = '';
                }
            });
        }

        // Función para eliminar una fila
        function deleteRow(event) {
            const row = event.target.closest('tr');
            row.remove();
        }

        // Inicializar lógica para el primer campo
        document.querySelectorAll('.rif-search').forEach(input => initializeRifSearch(input));

        // Lógica para Agregar Fila
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
                <input type="number" step="0.01" name="pagos[${pagoIndex}][monto]" class="monto-input" readonly style="background-color: #eee;" required>
            </td>
            <td><input type="text" name="pagos[${pagoIndex}][referencia]" required></td>
            <td><input type="date" name="pagos[${pagoIndex}][fecha]" class="fecha-input" required></td>
            <td><input type="checkbox" name="pagos[${pagoIndex}][status]"></td>
            <td><button type="button" class="delete-row">Eliminar</button></td>
        `;
            container.appendChild(newRow);

            // Inicializar la búsqueda para la nueva fila
            initializeRifSearch(newRow.querySelector('.rif-search'));

            // Agregar evento al botón de eliminar
            newRow.querySelector('.delete-row').addEventListener('click', deleteRow);

            pagoIndex++;
        });

        // Agregar evento al botón de eliminar de la primera fila (si existe)
        document.querySelectorAll('.delete-row').forEach(button => {
            button.addEventListener('click', deleteRow);
        });
    </script>

</body>

</html>