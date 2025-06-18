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
    $pagos = [];
    while ($row = $result->fetch_assoc()) {
        if ($referencia_anterior !== null && $row['referencia'] !== $referencia_anterior) {
            break;
        }
        $pagos[] = $row;
        $referencia_anterior = $row['referencia'];
    }

    $total = count($pagos);
    foreach ($pagos as $i => $row) {
        $html .= '
        <div style="font-family: Arial, sans-serif; margin: 30px; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 8px #eee;">
            <!-- Encabezado de la factura -->
            <div style="background: #f5f5f5; padding: 20px 30px; border-radius: 8px 8px 0 0;">
                <h2 style="margin: 0; color: #333;">' . $nombre . '</h2>
                <p style="margin: 2px 0 0 0; font-size: 14px;">RIF: ' . $rif . '</p>
                <p style="margin: 2px 0 0 0; font-size: 14px;">' . $direccion . '</p>
                <h1 style="text-align: right; color: #4CAF50; margin: -40px 0 0 0;">PAGO</h1>
            </div>
            <!-- Datos del propietario y factura -->
            <div style="padding: 20px 30px 10px 30px;">
                <div style="width: 60%; float: left;">
                    <h3 style="margin-bottom: 5px; color: #555;">Datos del Propietario</h3>
                    <p style="margin: 2px 0;"><strong>Nombre:</strong> ' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</p>
                </div>
                <div style="width: 38%; float: right; text-align: right;">
                    <p style="margin: 2px 0;"><strong>Nro. Pago:</strong> ' . htmlspecialchars($row['id']) . '</p>
                    <p style="margin: 2px 0;"><strong>Fecha:</strong> ' . htmlspecialchars($row['fecha']) . '</p>
                </div>
                <div style="clear: both;"></div>
            </div>
            <!-- Detalle del pago -->
            <div style="padding: 10px 30px 20px 30px;">
                <h3 style="color: #555; margin-bottom: 10px;">Detalle del Pago</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
                    <tr style="background: #e8f5e9;">
                        <th style="padding: 10px; border: 1px solid #c8e6c9; text-align: left;">Descripción</th>
                        <th style="padding: 10px; border: 1px solid #c8e6c9; text-align: right;">Monto</th>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #e0e0e0;">Pago de condominio - Ref: ' . htmlspecialchars($row['referencia']) . '</td>
                        <td style="padding: 10px; border: 1px solid #e0e0e0; text-align: right;">' . htmlspecialchars($row['monto']) . ' Bs</td>
                    </tr>
                </table>
                <table style="width: 100%; margin-top: 10px; font-size: 14px;">
                    <tr>
                        <td style="padding: 5px 0;"><strong>Estado:</strong> ' . htmlspecialchars($row['status']) . '</td>
                        <td style="padding: 5px 0; text-align: right;"><strong>Factura Afectada:</strong> ' . htmlspecialchars($row['factura_afectada']) . '</td>
                    </tr>
                </table>
            </div>
            <!-- Pie de página -->
            <div style="background: #f5f5f5; padding: 10px 30px; border-radius: 0 0 8px 8px; text-align: center; color: #888; font-size: 13px;">
                Gracias por su pago. Este documento es su comprobante oficial.
            </div>
        </div>';
        // Solo agrega salto de página si NO es el último comprobante
        if ($i < $total - 1) {
            $html .= '<div style="page-break-after: always;"></div>';
        }
    }

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('comprobante_pago.pdf', ['Attachment' => 0]);
} else {
    echo "No se encontraron pagos registrados.";
}

$conexion->close();

?>
