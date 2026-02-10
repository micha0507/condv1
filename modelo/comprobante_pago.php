<?php

include './conexion.php';

// Consulta para obtener los datos del condominio
$sql_condominio = "SELECT rif_admin, nombre_condominio, direccion_condominio FROM administrador LIMIT 1";
$result_condominio = $conexion->query($sql_condominio);

if ($result_condominio && $result_condominio->num_rows > 0) {
    $condominio = $result_condominio->fetch_assoc();
    $rif_admin = htmlspecialchars($condominio['rif_admin']);
    $nombre_condominio = htmlspecialchars($condominio['nombre_condominio']);
    $direccion_condominio = htmlspecialchars($condominio['direccion_condominio']);
    $hora_emision = date('h:i:s A') ;  

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
    $pagos = [];
    while ($row = $result->fetch_assoc()) {
        if ($referencia_anterior !== null && $row['referencia'] !== $referencia_anterior) {
            break;
        }
        $pagos[] = $row;
        $referencia_anterior = $row['referencia'];
    }

    $total = count($pagos);
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Comprobante de Pago</title>
        <style>
            body {
                background: #f8f8f8;
            }

            .comprobante {
                font-family: Arial, sans-serif;
                margin: 30px auto;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                box-shadow: 0 2px 8px #eee;
                background: #fff;
                max-width: 700px;
            }

            .comprobante-header {
                background: #f5f5f5;
                padding: 20px 30px;
                border-radius: 8px 8px 0 0;
            }

            .comprobante-header h2 {
                margin: 0;
                color: #333;
            }

            .comprobante-header h1 {
                text-align: right;
                color: #4CAF50;
                margin: -40px 0 0 0;
            }

            .comprobante-body {
                padding: 20px 30px 10px 30px;
            }

            .comprobante-footer {
                background: #f5f5f5;
                padding: 10px 30px;
                border-radius: 0 0 8px 8px;
                text-align: center;
                color: #888;
                font-size: 13px;
            }

            @media print {
                .btn-imprimir {
                    display: none !important;
                }

                body {
                    background: #fff;
                }
            }
        </style>
    </head>

    <body>
        <?php
        foreach ($pagos as $row) {
        ?>
            <div class="comprobante">
                <div class="comprobante-header">
                    <h2><?php echo $nombre_condominio; ?></h2>
                    <p style="margin: 2px 0 0 0; font-size: 14px;">RIF: <?php echo $rif_admin; ?></p>
                    <p style="margin: 2px 0 0 0; font-size: 14px;">Direccion: <?php echo $direccion_condominio; ?></p>
                    <h1>PAGO</h1>
                </div>
                <div class="comprobante-body">
                    <div style="width: 60%; float: left;">
                        <h3 style="margin-bottom: 5px; color: #555;">Datos del Propietario</h3>
                        <p style="margin: 2px 0;"><strong>Nombre:</strong> <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></p>
                    </div>
                    <div style="width: 38%; float: right; text-align: right;">
                        <p style="margin: 2px 0;"><strong>Nro. Pago:</strong> <?php echo htmlspecialchars($row['id']); ?></p>
                        <p style="margin: 2px 0;"><strong>Fecha:</strong> <?php echo htmlspecialchars($row['fecha']); ?></p>
                        <p style="margin: 2px 0;"><strong>Hora:</strong> <?php date_default_timezone_set('America/Caracas'); echo date('h:i:s A'); ?></p>

                    </div>
                    <div style="clear: both;"></div>
                    <h3 style="color: #555; margin-bottom: 10px;">Detalle del Pago</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
                        <tr style="background: #e8f5e9;">
                            <th style="padding: 10px; border: 1px solid #c8e6c9; text-align: left;">Descripción</th>
                            <th style="padding: 10px; border: 1px solid #c8e6c9; text-align: right;">Monto</th>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #e0e0e0;">Pago de condominio - Ref: <?php echo htmlspecialchars($row['referencia']); ?></td>
                            <td style="padding: 10px; border: 1px solid #e0e0e0; text-align: right;">
                                <?php echo number_format($row['monto'], 2, ',', '.'); ?> Bs
                            </td>
                        </tr>
                    </table>
                    <table style="width: 100%; margin-top: 10px; font-size: 14px;">
                        <tr>
                            <td style="padding: 5px 0;"><strong>Estado:</strong> <?php echo htmlspecialchars($row['status']); ?></td>

                            <td style="padding: 5px 0; text-align: right;"><strong>Factura Afectada:</strong> <?php echo htmlspecialchars($row['factura_afectada']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="comprobante-footer">
                    Gracias por su pago. Este documento es su comprobante oficial.
                </div>
                <button class="btn-imprimir" onclick="window.print()" style="margin: 20px auto 20px auto; display: block; padding: 10px 20px; background: #4CAF50; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                    Imprimir / Guardar PDF
                </button>
            </div>
            <br>
        <?php
        }
        ?>
    </body>

    </html>
<?php
} else {
    echo "No se encontraron pagos registrados.";
}

$conexion->close();

?>