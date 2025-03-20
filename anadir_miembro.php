<?php
include './modelo/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="./css/propietarios.css">
    <meta charset="UTF-8">
    <title>Añadir Miembro</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- barra de navegación -->
    <?php include 'navbar.php'; ?>
    <!-- PRINCIPAL -->
    <section class="principal">
        
        <?php
        $rif = $nombre = $apellido = $usuario = $pass = $email_propietario = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $tipo_rif = $_POST['tipo_rif'];
            $rif = $_POST['rif'];
            $rif = $tipo_rif . $rif; // Concatenar tipo_rif con rif
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $usuario = $_POST['usuario'];
            $pass = $_POST['pass'];
            $email_propietario = $_POST['email_propietario'];

            // Validaciones
            if (!preg_match("/^[a-zA-Z\s]+$/", trim($nombre))) {
            echo "El nombre solo debe contener letras";
            } elseif (!preg_match("/^[a-zA-Z\s]+$/", trim($apellido))) {
            echo "El apellido solo debe contener letras.";
            } elseif (strlen($pass) < 8 || strlen($pass) > 12) {
            echo "La contraseña debe tener entre 8 y 12 caracteres.";
            } elseif (!filter_var($email_propietario, FILTER_VALIDATE_EMAIL)) {
            echo "El email no es válido.";
            } else {
            // Verificar si el RIF ya está registrado
            $sql_check = "SELECT * FROM propietario WHERE rif = ?";
            $stmt_check = $conexion->prepare($sql_check);
            $stmt_check->bind_param("s", $rif);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                echo "El RIF ya está registrado.";
            } else {
                $sql = "INSERT INTO propietario (rif, nombre, apellido, usuario, pass, email_propietario) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssssss", $rif, $nombre, $apellido, $usuario, $pass, $email_propietario);

                if ($stmt->execute()) {
                echo "Nuevo miembro añadido exitosamente.";
                // Limpiar los campos del formulario
                $rif = $nombre = $apellido = $usuario = $pass = $email_propietario = "";
                } else {
                echo "Error: " . $sql . "<br>" . $conexion->error;
                }

                $stmt->close();
            }

            $stmt_check->close();
            $conexion->close();
            } }
        ?>

        <div class="carga_pago">
            <form id="anadirMiembroForm" method="post">
                <label for="rif">RIF:</label>
                <div>
                <select id="tipo_rif" name="tipo_rif" required>
                        <option value="V">V</option>
                        <option value="J">J</option>
                        <option value="G">G</option>
                        <option value="E">E</option>
                        <option value="C">C</option>
                    </select>
                    <input type="text" id="rif" placeholder="Ejemplo: 12345678" name="rif" value="<?php echo htmlspecialchars($rif); ?>" required><br>
     
                    </div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ejemplo: Juan" value="<?php echo htmlspecialchars($nombre); ?>" required><br>
                
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ejemplo: Pérez" value="<?php echo htmlspecialchars($apellido); ?>" required><br>
                
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ejemplo: juanperez123" value="<?php echo htmlspecialchars($usuario); ?>" required><br>
                
                <label for="pass">Contraseña:</label>
                <input type="password" id="pass" name="pass" placeholder="Entre 8 y 12 caracteres" value="<?php echo htmlspecialchars($pass); ?>" required><br>
                
                <label for="email_propietario">Email:</label>
                <input type="email" id="email_propietario" name="email_propietario" placeholder="Ejemplo: juan.perez@email.com" value="<?php echo htmlspecialchars($email_propietario); ?>" required><br>
                <button type="submit">Añadir Miembro</button>
            </form>
        </div>
    </section>
</body>
</html>