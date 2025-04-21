<!-- filepath: c:\xampp\htdocs\condv1\condominio_datos.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Condominio</title>
    <link rel="stylesheet" href="./css/estilos.css"> <!-- Opcional: Enlace a un archivo CSS -->
</head>
<body>
    <h1>Formulario de Datos del Condominio</h1>
    <form action="./modelo/guardar_datos_condominio.php" method="POST">
        <label for="rif">RIF:</label>
        <input type="text" id="rif" name="rif" placeholder="Ingrese el RIF" required>
        <br><br>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del condominio" required>
        <br><br>

        <label for="direccion">Dirección:</label>
        <textarea id="direccion" name="direccion" placeholder="Ingrese la dirección del condominio" rows="4" required></textarea>
        <br><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>