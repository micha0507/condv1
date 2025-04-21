<?php
include './conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $rif = $conexion->real_escape_string($_POST['rif']);
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $direccion = $conexion->real_escape_string($_POST['direccion']);

    // Consulta SQL para insertar los datos en la tabla datos_condominio
    $sql = "INSERT INTO datos_condominio (rif, nombre, direccion) VALUES ('$rif', '$nombre', '$direccion')";

    // Ejecutar la consulta y verificar si fue exitosa
    if ($conexion->query($sql) === TRUE) {
        echo "Datos guardados exitosamente.";
    } else {
        echo "Error al guardar los datos: " . $conexion->error;
    }

    // Cerrar la conexión
    $conexion->close();
} else {
    echo "Método de solicitud no permitido.";
}
?>