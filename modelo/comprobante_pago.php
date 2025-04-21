<?php

require_once '../vendor/autoload.php';
include './conexion.php';

use Dompdf\Dompdf;

// Consulta para obtener los datos del condominio
$sql_condominio = "SELECT rif, nombre, direccion FROM datos_condominio LIMIT 1";
$result_condominio = $conexion->query($sql_condominio);

if ($result_condominio && $result_condominio->num_rows > 0) {
    $condominio = $result_condominio->fetch_assoc();
    $rif = htmlspecialchars($condominio['rif']);
    $nombre = htmlspecialchars($condominio['nombre']);
    $direccion = htmlspecialchars($condominio['direccion']);
} else {
    $rif = "N/A";
    $nombre = "N/A";
    $direccion = "N/A";
}

// Consulta para obtener los pagos junto con el nombre y apellido del propietario
$sql = "
    SELECT p.*, pr.nombre, pr.apellido 
    FROM pagos p
    INNER JOIN propietario pr ON p.id_propietario = pr.id
    ORDER BY p.id DESC
";
$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    $referencia_anterior = null;
    $html = '';

    // Encabezado con los datos del condominio
    $html .= '<div style="font-family: Arial, sans-serif; margin: 20px;">';
    $html .= '<h1 style="text-align: center; color: #4CAF50;">Comprobante de Pago</h1>';
    $html .= '<p style="text-align: center; margin: 0;"><strong> ' . $rif . ' </strong></p>';
    $html .= '<p style="text-align: center; margin: 0;"><strong>' . $nombre . '</strong> </p>';
    $html .= '<p style="text-align: center; margin: 0;">' . $direccion . '</p>';
    $html .= '<hr style="margin: 20px 0; border: 1px solid #4CAF50;">';

    // Recorrer los pagos hasta encontrar una referencia diferente
    while ($row = $result->fetch_assoc()) {
        if ($referencia_anterior !== null && $row['referencia'] !== $referencia_anterior) {
            break; // Detener el bucle si la referencia es diferente
        }

        // Preparar los datos del comprobante para este pago
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        $html .= '<tr style="background-color: #f2f2f2; color: #333;">';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Campo</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Valor</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Nro.</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['id']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr style="background-color: #f9f9f9;">';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Propietario</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Fecha</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['fecha']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr style="background-color: #f9f9f9;">';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Estado</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['status']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Monto</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['monto']) . ' Bs</td>';
        $html .= '</tr>';
        $html .= '<tr style="background-color: #f9f9f9;">';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Referencia</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['referencia']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">Factura Afectada</td>';
        $html .= '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($row['factura_afectada']) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '<p style="text-align: center; margin-top: 20px; color: #555;">Gracias por su pago.</p>';
        $html .= '<div style="page-break-after: always;"></div>'; // Agregar salto de página

        $referencia_anterior = $row['referencia'];
    }

    // Instancia la clase Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // (Opcional) Establece el tamaño y la orientación del papel
    $dompdf->setPaper('A4', 'portrait');

    // Renderiza el HTML como PDF
    $dompdf->render();

    // Envía el PDF generado al navegador para su visualización (vista previa)
    $dompdf->stream('comprobante_pago.pdf', ['Attachment' => 0]);
} else {
    echo "No se encontraron pagos registrados.";
}

// Cerrar la conexión
$conexion->close();

?>