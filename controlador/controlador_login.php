<?php

session_start();

if (!empty($_POST["btningresar"])) {
    if (!empty($_POST["usuario"]) && !empty($_POST["password"])) {
        // Verificar reCAPTCHA
        $captcha = $_POST['g-recaptcha-response'];
        $secretkey = "6Le7gdEqAAAAAACSx6v_u50XJVCimS_fLHqrIGhe";
        $Ip = $_SERVER['REMOTE_ADDR'];
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$Ip");
        $atributos = json_decode($response, true);

        if ($atributos['success'] == false) {
            echo "<div class='alert alert-danger'>Por favor verifica que no eres un robot</div>";
        } else {
            $usuario = $_POST["usuario"];
            $password = $_POST["password"];

            // Consulta para verificar en la tabla 'propietario'
            $sql_propietario = $conexion->query("SELECT * FROM propietario WHERE usuario='$usuario' AND pass='$password'");
            
            // Consulta para verificar en la tabla 'personal_alto_nivel'
            $sql_personal = $conexion->query("SELECT * FROM administrador WHERE usuario_admin='$usuario' AND password_admin='$password'");

            if ($datos = $sql_propietario->fetch_object()) {
                // Si el usuario es un propietario
                $_SESSION["id"] = $datos->id;
                $_SESSION["nombre"] = $datos->nombre;
                $_SESSION["apellido"] = $datos->apellido;
                $_SESSION["rol"] = $datos->rol;

                header("location: ../inicio.php");
            } elseif ($datos = $sql_personal->fetch_object()) {
                // Si el usuario es personal de alto nivel
                $_SESSION["id_admin"] = $datos->id_admin;
                $_SESSION["usuario_admin"] = $datos->usuario_admin;
                $_SESSION["password_admin"] = $datos->password_admin;
                $_SESSION["rol_admin"] = $datos->rol_admin;

                header("location: ../condv1/index_admin.php");
            } else {
                // Si no se encuentra en ninguna de las dos tablas
                echo "<div class='alert alert-danger'>Acceso denegado</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Campos Vacíos</div>";
    }
}

?>