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
    
     <?php include 'navbar.php'; ?>
     
    
    <!-- PRINCIPAL -->
    <section class="principal">
        <h1>Carga del Pago:</h1>
        <!-- SECCION BUSQUEDA DE Propietario -->
        <section id="buscarPropietarioForm">  <section >
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
                        echo "</div>";
                    } else {
                        echo "<p>No se encontró ningún propietario con ese RIF/Cédula.</p>";
                    }
                }
                ?>
            </section>
            </section> 

        <!-- FORMULARIO -->
            <!-- SECCION RESIDENCIA -->
            <div class="carga_pago">
                <label for="residencia">Residencia:</label>
                <select id="residencia" name="residencia" required disabled>
            <option value="">Busque un propietario antes de seleccionar una residencia</option>
        </select>

        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['rif_cedula'])) {
                if (isset($row['id'])) {
                    $propietario_id = $row['id'];
                    $query_residencias = "SELECT r.nro AS numero_residencia FROM residencias r WHERE r.id_propietario = $propietario_id";
                    $result_residencias = $conexion->query($query_residencias);
                    
                    if ($result_residencias && $result_residencias->num_rows > 0) {
                        echo "<script>";
                        echo "document.getElementById('residencia').innerHTML = '';";
                        echo "document.getElementById('residencia').disabled = false;";
                        while ($residencia = $result_residencias->fetch_assoc()) {
                            echo "document.getElementById('residencia').innerHTML += '<option value=\"" . htmlspecialchars($residencia['numero_residencia']) . "\">" . htmlspecialchars($residencia['numero_residencia']) . "</option>';";
                        }
                        echo "</script>";
                    } else {
                        echo "<script>";
                        echo "document.getElementById('residencia').innerHTML = '<option value=\"\">No se encontraron residencias asociadas a este propietario</option>';";
                        echo "document.getElementById('residencia').disabled = true;";
                        echo "</script>";
                    }
                }
            }
            ?>
                </script>
            </div>
            
            <!-- SECCION PAGO -->
        <div class="carga_pago">
            <form method="POST" action="./modelo/procesar_pago.php">
                <input type="hidden" id="propietario_id" name="propietario_id" value="<?php echo $propietario_id; ?>">
                <label for="fecha">Fecha del Pago:</label>
                <input type="date" id="fecha" name="fecha" required>
                
                <label for="status">Estado del Pago:</label>
                <select id="status" name="status" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Validado">Validado</option>
                </select>
                
                <label for="monto">Monto:</label>
                <input type="number" id="monto" name="monto" step="0.01" required>
                
                <label for="referencia">Referencia:</label>
                <input type="text" id="referencia" name="referencia" required>
                
                <button type="submit">Registrar Pago</button>
            </form>
        </div>

    </section>
        </section>
</body>
</html>