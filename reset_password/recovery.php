<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluye los archivos de PHPMailer
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';

if (!empty($_POST["btnreset"])) {
    if (empty($_POST["email_admin"])) {
        echo "<div class='alert alert-danger'>Campo vacio</div>";
    } else {
        $email = $_POST["email_admin"];
        $conexion = new mysqli("localhost", "root", "", "condominio");

        if ($conexion->connect_error) {
            die("Connection failed: " . $conexion->connect_error);
        }

        // Buscar primero en administradores
        $stmt = $conexion->prepare("SELECT id_admin FROM administrador WHERE email_admin = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_type = null;
        $user_id = null;

        if ($result && $result->num_rows > 0) {
            $user_type = 'admin';
            $row = $result->fetch_assoc();
            $user_id = $row['id_admin'];
        } else {
            // Si no está en administradores, buscar en propietarios
            // Asegurar que la columna reset_token exista en la tabla propietario
            $resCheck = $conexion->query("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='condominio' AND TABLE_NAME='propietario' AND COLUMN_NAME='reset_token'");
            if ($resCheck) {
                $rowCheck = $resCheck->fetch_assoc();
                if (empty($rowCheck['cnt'])) {
                    // Intentar añadir la columna si no existe
                    @$conexion->query("ALTER TABLE propietario ADD COLUMN reset_token VARCHAR(255) NULL");
                }
            }

            $stmt2 = $conexion->prepare("SELECT id, email_propietario FROM propietario WHERE email_propietario = ?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($result2 && $result2->num_rows > 0) {
                $user_type = 'propietario';
                $row2 = $result2->fetch_assoc();
                $user_id = $row2['id'];
            }
        }

        if ($user_type !== null) {
            $token = bin2hex(random_bytes(50));
            if ($user_type === 'admin') {
                $upd = $conexion->prepare("UPDATE administrador SET reset_token = ? WHERE email_admin = ?");
                $upd->bind_param("ss", $token, $email);
            } else {
                $upd = $conexion->prepare("UPDATE propietario SET reset_token = ? WHERE email_propietario = ?");
                $upd->bind_param("ss", $token, $email);
            }
            $upd->execute();

            // Construir la URL base dinámicamente para evitar rutas hardcodeadas
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            // Cambia tu línea original por esta:
            $resetLink = $scheme . '://' . $host . $scriptDir . '/reset_password/new_password.php?token=' . urlencode($token);

            $subject = "Restablecimiento de Contraseña";
            $message = '
            <!doctype html>
            <html lang="es">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <meta charset="UTF-8" />
            </head>
            <body>
                <div style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px;">
                    <div style="max-width: 500px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 30px;">
                        <div style="text-align: center;">
                            <h2 style="color: #2d1ad9;">Restablecimiento de Contrase&ntilde;a</h2>
                        </div>
                        <p style="color: #333; font-size: 16px;">
                            Hola,<br>
                            Hemos recibido una solicitud para restablecer la contrase&ntilde;a de tu cuenta en el <b>Sistema Urbanizaci&oacute;n la Maroma</b>.
                        </p>
                        <p style="text-align: center; margin: 30px 0;">
                            <a href="' . $resetLink . '" style="background: linear-gradient(90deg, #5438f2 0%, #2d1ad9 100%); color: #fff; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 16px;">
                                Restablecer Contrase&ntilde;a
                            </a>
                        </p>
                        <p style="color: #555; font-size: 14px;">
                            Si no solicitaste este cambio, puedes ignorar este correo.<br>
                            Este enlace expirará después de usarlo una vez.
                        </p>
                        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                        <div style="text-align: center; color: #aaa; font-size: 12px;">
                            &copy; 2026 Urbanizaci&oacute;n la Maroma<br>
                            Contacto: UrbanizacionMaroma@gmail.com | Tel: (+58) 424-1234567
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ';
            // Configura PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'orquestasinfonica03@gmail.com'; // Cambia esto
                $mail->Password = 'wjuy eprn szah kfmp'; // Usa una contraseña de aplicación de Google aquí
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('orquestasinfonica03@gmail.com', 'Urbanizacion la Maroma'); // Cambia esto
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;

                $mail->send();
                echo "<div class='alert alert-success'>Se ha enviado un enlace de restablecimiento de contraseña a su correo electrónico.</div>";
                echo "<a href='https://mail.google.com/' target='_blank' title='Ir a Gmail' style='font-size:1rem; color:#04a1fc display:inline-block; margin-top:10px;'>
                        ir a <i class='fab fa-google'></i>mail
                      </a>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error al enviar el correo: {$mail->ErrorInfo}</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Correo electrónico no encontrado.</div>";
        }


        $conexion->close();
    }
}
