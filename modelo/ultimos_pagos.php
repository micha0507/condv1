<?php
// Include the database connection
include 'conexion.php';

// Query to get the last three payments
$query = "
    SELECT
        pagos.id AS pago_id,
        pagos.fecha,
        pagos.status,
        pagos.monto,
        pagos.referencia,
        propietario.nombre,
        propietario.apellido,
        residencias.nro
    FROM
        pagos
    INNER JOIN
        propietario ON pagos.id_propietario = propietario.id
    INNER JOIN
        residencias ON pagos.nro_residencia = residencias.id
    ORDER BY
        pagos.id DESC
    LIMIT 3
";

// Execute the query
$result = $conexion->query($query);

// Check if there are results
if ($result->num_rows > 0) {
    // Start the table
    echo "<div class='marcos_panel'>
        <table>
            <tr>
                <th>No.</th>
                <th>Fecha</th>
                <th>Status</th>
                <th># Residencia</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Monto</th>
                <th>Referencia</th>
            </tr>
            </div>";
        

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['pago_id']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['status']}</td>
                <td>{$row['nro']}</td>
                <td>{$row['nombre']}</td>
                <td>{$row['apellido']}</td>
                <td>{$row['monto']}</td>
                <td>{$row['referencia']}</td>
              </tr>";
    }

    // End the table
    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}

// Close the connection
$conexion->close();
?>