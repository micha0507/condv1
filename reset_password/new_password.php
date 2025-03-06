<?php
if (!empty($_GET["token"])) {
    $token = $_GET["token"];
    $conexion = new mysqli("localhost", "root", "", "condominio");

    if ($conexion->connect_error) {
        die("Connection failed: " . $conexion->connect_error);
    }

    $sql = $conexion->prepare("SELECT * FROM propietario WHERE reset_token = ?");
    $sql->bind_param("s", $token);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        if (!empty($_POST["btnresetpassword"])) {
            if (empty($_POST["new_password"]) || empty($_POST["confirm_password"])) {
                echo "<div class='alert alert-danger'>Campos vacíos</div>";
            } elseif ($_POST["new_password"] != $_POST["confirm_password"]) {
                echo "<div class='alert alert-danger'>Las contraseñas no coinciden</div>";
            } else {
                $new_password = password_hash($_POST["new_password"], PASSWORD_BCRYPT);
                $sql = $conexion->prepare("UPDATE propietario SET pass = ?, reset_token = NULL WHERE reset_token = ?");
                $sql->bind_param("ss", $new_password, $token);
                $sql->execute();

                echo "<div class='alert alert-success'>Contraseña restablecida con éxito</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Token inválido</div>";
    }

    $conexion->close();
} else {
    echo "<div class='alert alert-danger'>Token no proporcionado</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>
<body>
    <form method="post" action="">
        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" name="new_password" id="new_password" required>
        <br>
        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <br>
        <input type="submit" name="btnresetpassword" value="Restablecer Contraseña">
    </form>
</body>
</html>