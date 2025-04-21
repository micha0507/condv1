<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/pago_exitoso.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add" />
    <title>Pago Registrado</title>
</head>
<body>
<!-- Modal -->
    <div id="modalPagoRegistrado" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background-color: white; padding: 20px; border-radius: 10px; text-align: center; width: 300px;">
            <h2>Pago Registrado</h2>
            <p>El pago se ha registrado exitosamente.</p>
            <button id="btnImprimir" style="margin-top: 10px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir</button>
            <button id="btnCerrarModal" style="margin-top: 10px; padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">Cerrar</button>
        </div>
    </div>
</body>
</html>