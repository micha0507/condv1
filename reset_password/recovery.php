<?php
if (!empty($_POST["btnreset"])) {
    if (empty($_POST["email_propietario"])) {
        echo "<div class='alert alert-danger'>Campo vacio</div>";
    } else {
        $email = $_POST["email_propietario"];
        $conexion = new mysqli("localhost", "root", "", "condominio");

        if ($conexion->connect_error) {
            die("Connection failed: " . $conexion->connect_error);
        }

        $sql = $conexion->prepare("SELECT * FROM propietario WHERE email_propietario = ?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $sql = $conexion->prepare("UPDATE propietario SET reset_token = ? WHERE email_propietario = ?");
            $sql->bind_param("ss", $token, $email);
            $sql->execute();

            // Enviar correo electrónico con el token
            $resetLink = "http://mail.google/reset_password.php?token=" . $token;
            $subject = "Restablecimiento de contraseña";
            $message = "Haga clic en el siguiente enlace para restablecer su contraseña: " . $resetLink;
            $headers = "From: lgfr03@gmail.com";

                if (mail($email, $subject, $message, $headers)) {
                    echo "<div class='alert alert-success'>Se ha enviado un enlace de restablecimiento de contraseña a su correo electrónico.</div>";
                    echo "<a href='login.php'>Iniciar Sesión</a>";
                } else {
                    echo "<div class='alert alert-danger'>Error al enviar el correo electrónico.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Correo electrónico no encontrado.</div>";
            }
        } 

        $conexion->close();
    }

?>