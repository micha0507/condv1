<?php
// Incluye la conexion
include 'conexion.php';

// Para obtener ultimos pagos
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

// Ejecutar el script
$result = $conexion->query($query);

// Check if there are results
if ($result->num_rows > 0) {
    // Incicia la tabla
    echo "<div class='marcos_panel'>
        <table>
            <tr class='titulo_tabla'>
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
                <td style='background-color: " . ($row['status'] == "Pendiente" ? "#fffbd2;color:#e8ab54;" : "#c3fcc8;color:green;") . "border-radius: 6px;
                margin-top: 10px;
                margin-bottom: 10px;
                margin-left: 0px;
                font-size: 12px;
                font-style: bold;
                font-weight: bold;
                display: flex;
                justify-content: center;
                align-items: center;'>{$row['status']}</td>
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
