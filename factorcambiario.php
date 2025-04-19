<?php
include './modelo/conexion.php';

$message = ''; // Variable para almacenar el mensaje

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $factor = isset($_POST['factor']) ? $conexion->real_escape_string($_POST['factor']) : null;
    $monto_mensual = isset($_POST['monto_mensual']) ? $conexion->real_escape_string($_POST['monto_mensual']) : null;

    if ($factor && $monto_mensual) {
        $query = "INSERT INTO factor (factor, monto_mensual) VALUES ('$factor', '$monto_mensual')";
        if ($conexion->query($query) === TRUE) {
            $message = "<p>Registro insertado correctamente.</p>";
        } else {
            $message = "<p>Error al insertar el registro: " . $conexion->error . "</p>";
        }
    } else {
        $message = "<p>Por favor, complete todos los campos.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/factor.css">
    <title>Factor Cambiario</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="principal">
        <h1>Registrar Factor Cambiario</h1>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="">
            <label for="factor">Factor  ($ x Bs):</label>
            <input type="text" id="factor" name="factor" required>

            <label for="monto_mensual">Monto Mensual (USD):</label>
            <input type="number" id="monto_mensual" name="monto_mensual" step="0.01" required>

            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>