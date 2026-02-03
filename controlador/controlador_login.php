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

            // Consulta para propietario (obtener por usuario, luego verificar contraseña)
            $sql_propietario = $conexion->query("SELECT * FROM propietario WHERE usuario='$usuario'");

            // Consulta para administrador (obtener por usuario, luego verificar contraseña)
            $sql_personal = $conexion->query("SELECT * FROM administrador WHERE usuario_admin='$usuario'");

            $login_success = false;

            if ($datos = $sql_propietario->fetch_object()) {
                // Verificar contraseña (soporta hash y migración desde texto plano)
                if (password_verify($password, $datos->pass)) {
                    $login_success = true;
                    $_SESSION["id"] = $datos->id;
                    $_SESSION["nombre"] = $datos->nombre;
                    $_SESSION["apellido"] = $datos->apellido;
                    $_SESSION["rol"] = $datos->rol;

                    header("location: ./propietario.php");
                    exit;
                } elseif ($password === $datos->pass) {
                    // Contraseña almacenada en texto plano: migrar a hash
                    $newhash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_up = $conexion->prepare("UPDATE propietario SET pass = ? WHERE id = ?");
                    $stmt_up->bind_param("si", $newhash, $datos->id);
                    $stmt_up->execute();
                    $stmt_up->close();

                    // Iniciar sesión
                    $login_success = true;
                    $_SESSION["id"] = $datos->id;
                    $_SESSION["nombre"] = $datos->nombre;
                    $_SESSION["apellido"] = $datos->apellido;
                    $_SESSION["rol"] = $datos->rol;

                    header("location: ./propietario.php");
                    exit;
                }
            }

            if (!$login_success && ($datos = $sql_personal->fetch_object())) {
                if (password_verify($password, $datos->password_admin)) {
                    $_SESSION["id_admin"] = $datos->id_admin;
                    $_SESSION["usuario_admin"] = $datos->usuario_admin;
                    $_SESSION["rol_admin"] = $datos->rol_admin;

                    header("location: ./index_admin.php");
                    exit;
                } elseif ($password === $datos->password_admin) {
                    // Migrar contraseña texto plano a hash
                    $newhash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_up = $conexion->prepare("UPDATE administrador SET password_admin = ? WHERE id_admin = ?");
                    $stmt_up->bind_param("si", $newhash, $datos->id_admin);
                    $stmt_up->execute();
                    $stmt_up->close();

                    $_SESSION["id_admin"] = $datos->id_admin;
                    $_SESSION["usuario_admin"] = $datos->usuario_admin;
                    $_SESSION["rol_admin"] = $datos->rol_admin;

                    header("location: ./index_admin.php");
                    exit;
                }
            }

            // Si no se encontró o contraseña inválida
            echo "<div class='alert alert-danger'>Acceso denegado</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Campos Vacíos</div>";
    }
}

?>