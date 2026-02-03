<?php
include './modelo/conexion.php';

if (!$conexion) {
    die("<p>Error de conexión a la base de datos: " . mysqli_connect_error() . "</p>");
}

session_start();
if (empty($_SESSION['id_admin'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}

$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnregistrar'])) {
    // 1. Recibimos los datos
    $factor_raw = isset($_POST['factor']) ? trim($_POST['factor']) : '';
    $monto_raw = isset($_POST['monto_mensual']) ? trim($_POST['monto_mensual']) : '';

    // 2. Limpieza crucial: Convertimos coma en punto para que MySQL lo acepte
    $factor = str_replace(',', '.', $factor_raw);
    $monto_mensual = str_replace(',', '.', $monto_raw);

    if ($factor != '' && $monto_mensual != '') {
        // 3. Usamos "ss" (strings). MySQL es inteligente y los convertirá a DECIMAL(10,2) al insertar
        $stmt = $conexion->prepare("INSERT INTO factor (factor, monto_mensual) VALUES (?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ss", $factor, $monto_mensual);
            
            if ($stmt->execute()) {
                $message = "¡Registro guardado exitosamente!";
            } else {
                $message = "Error al ejecutar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error de preparación: " . $conexion->error;
        }
    } else {
        $message = "Error: Los campos están vacíos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factor Cambiario</title>
    <link rel="stylesheet" href="./css/factor.css">
    <link rel="icon" href="/img/ico_condo.ico">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="principal">
        <h1>Registrar Factor Cambiario</h1>
        
        <?php if (!empty($message)): ?>
            <script>
                alert("<?php echo $message; ?>");
            </script>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="factor">Factor ($ x Bs):</label>
            <div class="factor">
                <input type="text" id="factor" name="factor" required>
                <button type="button" onclick="obtenerFactorBCV()">Obtener factor </button>
            </div>

            <label for="monto_mensual">Monto Mensual (USD):</label>
            <input type="number" id="monto_mensual" name="monto_mensual" step="0.01" required>

            <button type="submit" name="btnregistrar">Registrar</button>

            <hr>
            <label>Generar Factura:</label>
            <button type="button" onclick="ejecutarFacturacion()">Generar</button>
        </form>
    </div>

    <script>
    // Tu función existente para obtener el factor del BCV
    function obtenerFactorBCV() {
        fetch('./modelo/obtiene_factor.php')
            .then(response => response.text())
            .then(data => {
                let match = data.match(/Valor USD encontrado: ([\d.,]+)/);
                if (match && match[1]) {
                    // Limpieza de formato para que sea un número válido (ej: 36,50 -> 36.50)
                    let valorLimpio = match[1].replace(/\./g, '').replace(',', '.');
                    document.getElementById('factor').value = valorLimpio;
                } else {
                    alert('No se pudo obtener el valor del BCV.');
                }
            })
            .catch(() => alert('Error al consultar el valor del BCV.'));
    }

    // Tu función de facturación con el alert de error solicitado
    function ejecutarFacturacion() {
        fetch('./modelo/generar_facturas.php')
        .then(response => {
            if (!response.ok) throw new Error();
            return response.text();
        })
        .then(data => {
            alert("¡Facturación completada con éxito!");
        })
        .catch(() => {
            alert("Hubo un error: No se pudo completar el proceso de facturación.");
        });
    }
    </script>
</body>
</html>