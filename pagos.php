<!-- filepath: c:\xampp\htdocs\condv1\pagos.php -->
<?php
date_default_timezone_set('America/Caracas'); // Configura la zona horaria correcta
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos</title>
    <link rel="stylesheet" href="./css/tabla_pagos.css"> <!-- Opcional: Enlace a un archivo CSS -->
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="principal">
        <h1>Listado de Pagos</h1>

        <!-- Formulario de filtros -->
        <form method="GET" action="">
            <label for="status">Estado:</label>
            <select id="status" name="status">
                <option value="">Todos</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Validado">Validado</option>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : ''; ?>">

            <label for="propietario">Propietario:</label>
            <input type="text" id="propietario" name="propietario" placeholder="Nombre o Apellido">

            <label for="periodo">Período (MM-AAAA):</label>
            <input type="text" id="periodo" name="periodo" placeholder="Ejemplo: 03-2025">

            <button type="submit">Filtrar</button>
        </form>

        <!-- Tabla de pagos -->
        <form method="POST" action="procesar_validacion.php">
            <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Fecha del Pago</th>
                        <th>Fecha de Registro</th>
                        <th>Estado</th>
                        <th>Propietario</th>
                        <th>Monto</th>
                        <th>Referencia</th>
                        <th>Factura Afectada</th>
                        <th>Validado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include './modelo/conexion.php';

                    // Construir la consulta con filtros
                    $where = [];

                    // Filtro por estado
                    if (!empty($_GET['status'])) {
                        $status = $conexion->real_escape_string($_GET['status']);
                        $where[] = "p.status = '$status'";
                    }

                    // Filtro por fecha (solo si se selecciona explícitamente)
                    if (!empty($_GET['fecha'])) {
                        $fecha = $conexion->real_escape_string($_GET['fecha']);
                        $where[] = "DATE(p.fecha) = '$fecha'";
                    }

                    // Filtro por propietario
                    if (!empty($_GET['propietario'])) {
                        $propietario = $conexion->real_escape_string($_GET['propietario']);
                        $where[] = "(pr.nombre LIKE '%$propietario%' OR pr.apellido LIKE '%$propietario%')";
                    }

                    // Filtro por período
                    if (!empty($_GET['periodo'])) {
                        $periodo = $conexion->real_escape_string($_GET['periodo']);
                        $where[] = "f.periodo = '$periodo'";
                    }

                    // Si no se selecciona ningún filtro, aplicar la fecha actual por defecto
                    if (empty($_GET['status']) && empty($_GET['fecha']) && empty($_GET['propietario']) && empty($_GET['periodo'])) {
                        $fecha = date('Y-m-d');
                        $where[] = "DATE(p.fecha) = '$fecha'";
                    }

                    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

                    // Consulta para obtener los pagos con filtros y el INNER JOIN con facturas
                    $sql = "
                        SELECT p.id, p.fecha, p.fecha_registro, p.status, CONCAT(pr.nombre, ' ', pr.apellido) AS propietario, 
                               p.monto, p.referencia, p.factura_afectada, f.periodo
                        FROM pagos p
                        INNER JOIN propietario pr ON p.id_propietario = pr.id
                        INNER JOIN facturas f ON p.factura_afectada = f.id_factura
                        $where_sql
                        ORDER BY p.id DESC
                    ";

                    $result = $conexion->query($sql);

                    if ($result && $result->num_rows > 0) {
                        $has_pending = false; // Variable para habilitar el botón solo si hay pagos pendientes
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['fecha_registro']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['propietario']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['monto']) . " Bs</td>";
                            echo "<td>" . htmlspecialchars($row['referencia']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['factura_afectada']) . "</td>";

                            // Mostrar checkbox solo si el estado es "Pendiente"
                            if ($row['status'] === 'Pendiente') {
                                echo "<td style='text-align: center;'><input type='checkbox' name='validar[]' value='" . htmlspecialchars($row['id']) . "'></td>";
                                $has_pending = true; // Hay al menos un pago pendiente
                            } else {
                                echo "<td style='text-align: center;'>-</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align: center;'>No se encontraron resultados.</td></tr>";
                    }

                    $conexion->close();
                    ?>
                </tbody>
            </table>
            <!-- Botón Confirmar habilitado solo si hay pagos pendientes -->
            <button type="submit" style="margin-top: 20px;" <?php echo isset($has_pending) && $has_pending ? '' : 'disabled'; ?>>Confirmar</button>
        </form>
    </div>
</body>
</html>