<?php
include './modelo/conexion.php';
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
     <nav class="navegacion">
     <?php include 'navbar.php'; ?>
     </nav>
    
    <!-- PRINCIPAL -->
    <section class="principal">
        <form id="buscarPropietarioForm" method="post">
            <label for="rif_cedula">RIF/Cédula del Propietario:</label>
            <input type="text" id="rif_cedula" name="rif_cedula" required>
            <button type="submit">Buscar</button>
        </form>

        <div id="resultadoPropietario"></div>
        <div id="nombrePropietario"></div>

        <script>
            $(document).ready(function() {
                $('#buscarPropietarioForm').on('submit', function(event) {
                    event.preventDefault();
                    var rif_cedula = $('#rif_cedula').val();

                    $.ajax({
                        url: 'buscar_propietario.php',
                        type: 'POST',
                        data: { rif_cedula: rif_cedula },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.error) {
                                $('#resultadoPropietario').html(data.error);
                                $('#resultadoResidencias').html('<option value="">Seleccione una residencia</option>');
                                $('#nombrePropietario').html('');
                            } else {
                                $('#resultadoPropietario').html('Propietario encontrado');
                                $('#id_propietario').val(data.id); // Asignar el ID del propietario al campo oculto
                                $('#nombrePropietario').html('Nombre: ' + data.nombre + ' ' + data.apellido);
                                buscarResidencias(data.id);
                            }
                        }
                    });
                });

                function buscarResidencias(id_propietario) {
                    $.ajax({
                        url: 'busca_residencias.php',
                        type: 'POST',
                        data: { id_propietario: id_propietario },
                        success: function(response) {
                            var data = JSON.parse(response);
                            var select = $('#resultadoResidencias');
                            select.html('<option value="">Seleccione una residencia</option>');
                            if (data.error) {
                                select.append('<option value="">' + data.error + '</option>');
                            } else {
                                data.residencias.forEach(function(residencia) {
                                    select.append('<option value="' + residencia + '">' + residencia + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        </script>
        <!-- FORMULARIO -->
        <div class="carga_pago">
            <form id="cargarPagoForm" method="post" action="carga_pago_modelo.php">
                <input type="hidden" id="id_propietario" name="id_propietario">
                
                <label for="nro_residencia">Número de Residencia:</label>
                <select id="resultadoResidencias" name="nro_residencia" required>
                    <option value="">Seleccione una residencia</option>
                </select>
                
                <label for="monto">Monto:</label>
                <input type="number" step="0.1" id="monto" name="monto" required>
                
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
                
                <label for="referencia">Referencia:</label>
                <input type="text" id="referencia" name="referencia" required>
                
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Validado">Validado</option>
                </select>
                
                <button type="submit">Cargar Pago</button>
            </form>

            <script>
                $(document).ready(function() {
                    $('#cargarPagoForm').on('submit', function(event) {
                        event.preventDefault();
                        var formData = $(this).serialize();

                        $.ajax({
                            url: 'carga_pago_modelo.php',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                var data = JSON.parse(response);
                                if (data.error) {
                                    alert(data.error);
                                } else {
                                   
                                    window.location.href = 'pago_registrado.html'; // Redirigir a pago_registrado.html
                                }
                            }
                        });
                    });
                });
            </script>
        </div>
    </section>
</body>
</html>