<?php
include './modelo/conexion.php';

session_start();
if (empty($_SESSION['id_admin'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/publicacion.css">
    <title>Publicaciones</title>
       <link rel="icon" href="/img/ico_condo.ico">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="pub-list-container">
    <h2 class="pub-list-title">Publicaciones recientes</h2>
    <div class="pub-list">
    <?php
    // Obtener las últimas 5 publicaciones
    $sql = "SELECT contenido, id_post FROM post ORDER BY id_post DESC LIMIT 5";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            echo '<article class="post-card">';
            echo $fila['contenido'];
            echo '</article>';
        }
    } else {
        echo '<p class="no-posts">No hay publicaciones disponibles.</p>';
    }

    // Cerrar conexión
    $conexion->close();
    ?>
    </div>
</div>
</body>
</html>
