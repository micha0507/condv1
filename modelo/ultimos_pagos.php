<?php

include 'conexion.php';

// Para obtener los datos de la tabla pagos junto con nombre y apellido del propietario
$query = "
    SELECT
        pagos.id AS pago_id,
        pagos.fecha,
        pagos.fecha_registro,
        pagos.status,
        pagos.monto,
        pagos.referencia,
        pagos.factura_afectada,
        propietario.nombre,
        propietario.apellido
    FROM
        pagos
    INNER JOIN
        propietario ON pagos.id_propietario = propietario.id
    ORDER BY
        pagos.id DESC
    LIMIT 3
";

// Ejecutar el script
$result = $conexion->query($query);

// Se comprueba si hay resultados
if ($result->num_rows > 0) {
    // Inicia la tabla
    echo "<div class='marcos_panel'>
        <table border='1'>
            <tr class='titulo_tabla'>
                <th>No.</th>
                <th>Fecha</th>
                <th>Fecha Registro</th>
                <th>Status</th>
                <th>Propietario</th>
                <th>Monto</th>
                <th>Referencia</th>
                <th>Factura Afectada</th>
            </tr>";

    // Datos de salida de cada fila
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['pago_id']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['fecha_registro']}</td>
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
                <td>{$row['nombre']} {$row['apellido']}</td>
                <td>" . number_format($row['monto'], 2, ',', '.') . "</td>
                <td>{$row['referencia']}</td>
                <td>{$row['factura_afectada']}</td>
              </tr>";
    }

    // Cierra la tabla
    echo "</table></div>";
} else {
    echo "No se encontraron resultados.";
}
