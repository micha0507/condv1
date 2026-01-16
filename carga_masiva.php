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
                            <th>Monto</th>
                            <th>Referencia</th>
                            <th>Factura Afectada</th>
                            <th>Fecha</th>
                            <th>Status</th>
                            <th>Acciones</th> <!-- Nueva columna para el botón de eliminar -->
                        </tr>
                    </thead>
                    <tbody id="pagos-container">
                        <tr>
                            <td>
                                <input type="text" name="pagos[0][rif]" class="rif-search" placeholder="Buscar RIF..." required>
                                <div class="rif-results"></div>
                            </td>
                            <td><input type="number" step="0.01" name="pagos[0][monto]" required></td>
                            <td><input type="text" name="pagos[0][referencia]" required></td>
                            <td><input type="text" name="pagos[0][factura_afectada]" required></td>
                            <td><input type="date" name="pagos[0][fecha]" class="fecha-input" required></td>
                            <td><input type="checkbox" name="pagos[0][status]"></td>
                            <td></td> <!-- Sin botón de eliminar en la primera fila -->
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
        // Búsqueda activa de propietarios por RIF
        function initializeRifSearch(input) {
            input.addEventListener('input', function () {
                const query = this.value.trim();
                const resultsContainer = this.nextElementSibling;

                if (query.length > 2) {
                    fetch(`modelo/busca_propietario.php?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = ''; // Limpiar resultados anteriores
                            if (data.length === 0) {
                                resultsContainer.innerHTML = '<div>No se encontraron resultados</div>';
                            } else {
                                data.forEach(propietario => {
                                    const div = document.createElement('div');
                                    div.textContent = `${propietario.rif} - ${propietario.nombre} ${propietario.apellido}`;
                                    div.addEventListener('click', () => {
                                        this.value = propietario.rif;
                                        resultsContainer.innerHTML = ''; // Limpiar resultados al seleccionar
                                    });
                                    resultsContainer.appendChild(div);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error en la búsqueda:', error);
                            resultsContainer.innerHTML = '<div>Error al buscar resultados</div>';
                        });
                } else {
                    resultsContainer.innerHTML = ''; // Limpiar resultados si la consulta es muy corta
                }
            });
        }

        // Inicializar búsqueda activa para los campos existentes
        document.querySelectorAll('.rif-search').forEach(input => initializeRifSearch(input));

        // Agregar nueva fila
        let pagoIndex = 1;
        document.getElementById('add-pago').addEventListener('click', () => {
            const container = document.getElementById('pagos-container');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input type="text" name="pagos[${pagoIndex}][rif]" class="rif-search" placeholder="Buscar RIF..." required>
                    <div class="rif-results"></div>
                </td>
                <td><input type="number" step="0.01" name="pagos[${pagoIndex}][monto]" required></td>
                <td><input type="text" name="pagos[${pagoIndex}][referencia]" required></td>
                <td><input type="text" name="pagos[${pagoIndex}][factura_afectada]" required></td>
                <td><input type="date" name="pagos[${pagoIndex}][fecha]" class="fecha-input" required></td>
                <td><input type="checkbox" name="pagos[${pagoIndex}][status]"></td>
                <td><button type="button" class="delete-row">Eliminar</button></td>
            `;
            container.appendChild(newRow);

            // Inicializar búsqueda activa para el nuevo campo
            const newInput = newRow.querySelector('.rif-search');
            initializeRifSearch(newInput);

            // Agregar evento al botón de eliminar
            newRow.querySelector('.delete-row').addEventListener('click', () => {
                newRow.remove();
            });

            pagoIndex++;
        });

        // Agregar evento de eliminar a las filas existentes (si es necesario)
        document.querySelectorAll('.delete-row').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('tr').remove();
            });
        });
    </script>
</body>
</html>