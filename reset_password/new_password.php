<?php
$token_valid = false;
$message_html = '';
$token = '';

$conexion = new mysqli("localhost", "root", "", "condominio");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Si viene por GET o por POST (al enviar el formulario mantenemos el token en un input hidden)
if (!empty($_GET['token'])) {
    $token = $_GET['token'];
} elseif (!empty($_POST['token'])) {
    $token = $_POST['token'];
}

if (!empty($token)) {
    $sql = $conexion->prepare("SELECT id_admin FROM administrador WHERE reset_token = ?");
    $sql->bind_param("s", $token);
    $sql->execute();
    $result = $sql->get_result();

    if ($result && $result->num_rows > 0) {
        $token_valid = true;
        $row = $result->fetch_assoc();
        $admin_id = $row['id_admin'];

        // Procesar envío del formulario
        if (!empty($_POST["btnresetpassword"])) {
            $new_password = $_POST["new_password"] ?? '';
            $confirm_password = $_POST["confirm_password"] ?? '';

            if (empty($new_password) || empty($confirm_password)) {
                $message_html = "<div class='alert alert-danger'>Campos vacíos</div>";
            } elseif ($new_password !== $confirm_password) {
                $message_html = "<div class='alert alert-danger'>Las contraseñas no coinciden</div>";
            } elseif (strlen($new_password) < 6 || strlen($new_password) > 50) {
                $message_html = "<div class='alert alert-danger'>La contraseña debe tener entre 6 y 50 caracteres.</div>";
            } else {
                // Guardar contraseña en texto plano (NO RECOMENDADO)
                $plain = $new_password;
                $update = $conexion->prepare("UPDATE administrador SET password_admin = ?, reset_token = NULL WHERE id_admin = ?");
                $update->bind_param("si", $plain, $admin_id);
                if ($update->execute()) {
                    $message_html = "<div class='alert alert-success' style='background:linear-gradient(90deg,#e6ffed,#d4f7e2);border:1px solid #2ecc71;color:#155724;padding:12px 16px;border-radius:6px;box-shadow:0 2px 6px rgba(46,204,113,0.15);font-weight:600;display:inline-block;'>Contraseña restablecida con éxito. <a href='../login.php'>Iniciar sesión</a></div>";
                    $token_valid = false; // evitar reuso del formulario
                } else {
                    $message_html = "<div class='alert alert-danger'>Error al actualizar la contraseña.</div>";
                }
            }
        }
    } else {
        $message_html = <<<HTML
<div class="d-flex justify-content-center align-items-center" style="min-height:30vh;padding:30px;">
  <div class="card text-white bg-danger shadow" style="max-width:420px;width:100%;">
   <div class="card-body text-center">
     <div class="mb-2"><i class="fas fa-exclamation-triangle fa-3x"></i></div>
     <h5 class="card-title">Token inválido o expirado</h5>
     <p class="card-text">No se pudo verificar la solicitud. Por favor solicita un nuevo enlace para restablecer la contraseña.</p>
     <div class="d-flex justify-content-center gap-2">
      <a href="../reset_password.php" class="btn btn-light btn-sm">Solicitar nuevo enlace</a>
      <a href="../login.php" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
     </div>
   </div>
  </div>
</div>
HTML;
    }
}

if (empty($token)) {
    $message_html = <<<HTML
<div class="d-flex justify-content-center align-items-center" style="min-height:30vh;padding:30px;">
  <div class="card text-white bg-danger shadow" style="max-width:420px;width:100%;">
   <div class="card-body text-center">
     <div class="mb-2"><i class="fas fa-exclamation-triangle fa-3x"></i></div>
     <h5 class="card-title">Token no proporcionado</h5>
     <p class="card-text">No se pudo verificar la solicitud. Por favor solicita un nuevo enlace para restablecer la contraseña.</p>
     <div class="d-flex justify-content-center gap-2">
      <a href="../reset_password.php" class="btn btn-light btn-sm">Solicitar nuevo enlace</a>
      <a href="../login.php" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
     </div>
   </div>
  </div>
</div>
HTML;
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="../css/bootstrap.css">
   <link rel="stylesheet" type="text/css" href="../css/style.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
   <title>Restablecer Contraseña</title>
      <link rel="icon" href="/img/ico_condo.ico">
</head>

<body>
   <img class="wave" src="../img/wave.png">
   <div class="container">
      <div class="img">
         <img src="">
      </div>
      <div class="login-content animate__animated animate__fadeInUp">
         <?php
         // Mostrar mensajes o la tarjeta de error
         if (!empty($message_html)) {
             echo $message_html;
         }

         // Mostrar formulario solo si el token es válido
         if ($token_valid): ?>
         <form method="post" action="">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES); ?>">
            <img src="/img/icono_condo.jpg">
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Nueva Contraseña:</h5>
                  <input type="password" name="new_password" id="new_password" class="input" minlength="6" maxlength="50" required>
               </div>
            </div>
            <div class="input-div pass">
               <div class="i">
                  <i class="fas fa-lock"></i>
               </div>
               <div class="div">
                  <h5>Confirmar Contraseña:</h5>
                  <input type="password" name="confirm_password" id="confirm_password" class="input" minlength="6" maxlength="50" required>
               </div>
            </div>
            <div class="view">
               <div class="fas fa-eye verPassword" id="verPassword"></div>
            </div>

            <input type="submit" name="btnresetpassword" value="Restablecer Contraseña" class="btn">
         </form>
         <?php endif; ?>
      </div>
   </div>

   <script src="../js/fontawesome.js"></script>
   <script src="../js/main.js"></script>
   <script src="../js/main2.js"></script>
   <script src="../js/jquery.min.js"></script>
   <script src="../js/bootstrap.js"></script>
   <script src="../js/bootstrap.bundle.js"></script>

   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const eye = document.getElementById('verPassword');
         const pass1 = document.getElementById('new_password');
         const pass2 = document.getElementById('confirm_password');
         if (!eye || !pass1 || !pass2) return;
         let visible = false;
         eye.addEventListener('click', function() {
            visible = !visible;
            pass1.type = visible ? 'text' : 'password';
            pass2.type = visible ? 'text' : 'password';
            eye.classList.toggle('fa-eye-slash', visible);
            eye.classList.toggle('fa-eye', !visible);
         });
      });
   </script>

</body>

</html>