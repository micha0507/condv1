<?php
include './modelo/conexion.php';

session_start();
if (empty($_SESSION['id_admin'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/fondos.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add" />
    <title>Fondos</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="contenido">
        <h1>Registrar Gastos Eventuales</h1>
        <form id="gastoForm" action="./modelo/procesar_gasto.php" method="POST">
            <label for="concepto">Concepto:</label>
            <input type="text" id="concepto" name="concepto" required>
            
            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria" required>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Servicios Públicos">Servicios Públicos</option>
                <option value="Reparaciones">Reparaciones</option>
                <option value="Limpieza">Limpieza</option>
                <option value="Nomina">Nomina</option>
                <option value="Otros">Otros</option>
            </select>
            
            <label for="monto">Monto:</label>
            <input type="number" id="monto" name="monto" step="0.01"  placeholder="12.500,00">
            
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" max="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-01'); ?>" required>
            
            <button type="submit">Registrar Gasto</button>
        </form>

        <!-- Fondo oscuro para el modal -->
        <div id="modalFondo" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 999;"></div>

        <!-- Modal para confirmar el registro del gasto -->
        <div id="modalExito" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(255, 255, 255, 1); z-index: 1000; padding: 20px; border-radius: 8px; width: 300px; text-align: center; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #4CAF50;">¡Gasto Eventual registrado exitosamente!</h2>
            <button id="btnImprimir" style="margin: 10px; padding: 10px 20px; background-color: #1ecaf5; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir</button>
            <button id="btnCerrarModal" style="margin: 10px; padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">Cerrar</button>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#gastoForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Mostrar el modal de éxito y el fondo oscuro
                    $('#modalFondo').fadeIn();
                    $('#modalExito').fadeIn();
                    // Limpiar el formulario
                    $('#gastoForm')[0].reset();
                },
                error: function() {
                    alert('Error al registrar el gasto.');
                }
            });
        });

        $('#btnCerrarModal').on('click', function() {
            $('#modalFondo').fadeOut();
            $('#modalExito').fadeOut();
        });

        $('#btnImprimir').on('click', function() {
            window.open('./modelo/comprobante_gasto_eventual.php', '_blank');
        });
    });
    </script>
</body>
</html>