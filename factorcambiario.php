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
    <style>
        /* Fondo del Modal */
        .modal-container {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            /* Efecto de desenfoque al fondo */
        }

        /* Caja de contenido del Modal */
        .modal-content {
            background-color: #fff;
            margin: 12% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border-top: 8px solid #2c3e50;
            /* Color del navbar */
        }

        .modal-content h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .modal-content p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Contenedor de botones */
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        /* Estilo base de botones del modal */
        .btn-modal {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-confirm {
            background-color: #27ae60;
            /* Verde éxito */
            color: white;
        }

        .btn-confirm:hover {
            background-color: #219150;
        }

        .btn-cancel {
            background-color: #e74c3c;
            /* Rojo cancelar */
            color: white;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
        }
    </style>
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
            <label>Acción de Facturación:</label>
            <button type="button" class="btn_generar" onclick="mostrarModalConfirmacion()" style="background-color: #2c3e50; color: white;">
                Generar Facturas del Mes
            </button>
        </form>
    </div>

    <?php
    // Consulta para obtener el último registro de factor y monto
    $sql_ultimo = "SELECT factor, monto_mensual FROM factor ORDER BY id DESC LIMIT 1";
    $resultado_ultimo = $conexion->query($sql_ultimo);

    $ultimo_f = "0.00";
    $ultimo_m = "0.00";

    if ($resultado_ultimo && $resultado_ultimo->num_rows > 0) {
        $fila = $resultado_ultimo->fetch_assoc();
        $ultimo_f = number_format($fila['factor'], 2, ',', '.');
        $ultimo_m = number_format($fila['monto_mensual'], 2, ',', '.');
    }
    ?>
    <!-- Modal de Confirmación -->
    <div id="modalFactura" class="modal-container">
        <div class="modal-content">
            <h2>Confirmar Proceso</h2>
            <p>
                ¿Deseas generar las facturas utilizando los últimos datos registrados?<br><br>
                <strong>Factor:</strong> <?php echo $ultimo_f; ?> Bs.<br>
                <strong>Monto Mensual:</strong> <?php echo $ultimo_m; ?> $
            </p>

            <div class="modal-buttons">
                <button onclick="ejecutarFacturacion()" class="btn-modal btn-confirm">
                    ✅ Sí, Generar
                </button>
                <button onclick="cerrarYRegresar()" class="btn-modal btn-cancel">
                    ❌ No, Registrar Nuevo
                </button>
            </div>
        </div>
    </div>

    <script>
        // Funciones del Modal
        function mostrarModalConfirmacion() {
            document.getElementById('modalFactura').style.display = 'block';
        }

        function cerrarYRegresar() {
            document.getElementById('modalFactura').style.display = 'none';
            // Limpiar campos y enfocar para que el usuario registre el nuevo valor
            document.getElementById('factor').focus();
            alert("Por favor, obtenga el factor o ingrese los nuevos datos y presione 'Registrar' antes de generar.");
        }

        function obtenerFactorBCV() {
            fetch('./modelo/obtiene_factor.php')
                .then(response => response.text())
                .then(data => {
                    let match = data.match(/Valor USD encontrado: ([\d.,]+)/);
                    if (match && match[1]) {
                        let valorLimpio = match[1].replace(/\./g, '').replace(',', '.');
                        document.getElementById('factor').value = valorLimpio;
                    } else {
                        alert('No se pudo obtener el valor del BCV.');
                    }
                })
                .catch(() => alert('Error al consultar el valor del BCV.'));
        }

        function ejecutarFacturacion() {
            document.getElementById('modalFactura').style.display = 'none'; // Cerrar modal al confirmar

            fetch('./modelo/generar_facturas.php')
                .then(response => {
                    if (!response.ok) throw new Error();
                    return response.text();
                })
                .then(data => {
                    // El backend ya tiene la lógica de no duplicar por periodo
                    alert("¡Facturación completada con éxito!");
                })
                .catch(() => {
                    alert("Hubo un error: No se pudo completar el proceso de facturación.");
                });
        }
    </script>
</body>

</html>