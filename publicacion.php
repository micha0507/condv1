<?php
// Incluimos el archivo de conexión


// Consulta para obtener el último registro de la tabla 'post'
$sql = "SELECT contenido FROM post ORDER BY id_post DESC LIMIT 1";
$resultado = $conexion->query($sql);

// Verificamos si hay resultados
if ($resultado->num_rows > 0) {
    // Obtenemos el contenido del último registro
    $fila = $resultado->fetch_assoc();
    echo $fila['contenido'];
} else {
    echo "No hay registros en la tabla 'post'.";
}

// Cerramos la conexión
