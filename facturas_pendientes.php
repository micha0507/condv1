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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="./css/facturas_pendientes.css">
   
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add" />
    <title>Panel Administrador</title>
       <link rel="icon" href="/img/ico_condo.ico">
</head>
<body>
<!-- Aquí va el código de la página principal del panel de administrador -->
    
    <?php include 'navbar.php'; ?>

    <!-- pantalla principal -->
    <div class="principal">
        <div class="tabla">

       <?php
// Obtener el último periodo existente en la tabla facturas
$sql_periodo = "SELECT MAX(periodo) as ultimo_periodo FROM facturas";
$res_p = $conexion->query($sql_periodo);
$ultimo_periodo = ($res_p->num_rows > 0) ? $res_p->fetch_assoc()['ultimo_periodo'] : null;

if ($ultimo_periodo) {
    // MODIFICACIÓN: Agregamos r.nro (residencia) y el JOIN con la tabla residencias
    $sql = "SELECT f.id_factura, f.periodo, f.status, p.rif, p.nombre, p.apellido, r.nro as residencia
            FROM facturas f
            INNER JOIN propietario p ON f.propietario_id = p.id 
            LEFT JOIN residencias r ON p.id = r.id_propietario
            WHERE f.status = 'Pendiente' AND f.periodo = ?";
            
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $ultimo_periodo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='tabla_facturas'>";
        // Agregamos la cabecera "Residencia"
        echo "<tr><th>Factura</th><th>Periodo</th><th>Status</th><th>RIF</th><th>Nombre</th><th>Apellido</th><th>Residencia</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_factura']) . "</td>";
            echo "<td>" . htmlspecialchars($row['periodo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rif']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['apellido']) . "</td>";
            // Imprimimos el número de residencia
            echo "<td>" . (!empty($row['residencia']) ? htmlspecialchars($row['residencia']) : 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay facturas pendientes para el último periodo ($ultimo_periodo).</p>";
    }
    $stmt->close();
} else {
    echo "<p>No se pudo determinar el último periodo.</p>";
}
?>
    </div>
     </div>
</body>
</html>