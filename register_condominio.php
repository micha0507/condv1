<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <link href="https://tresplazas.com/web/img/big_punto_de_venta.png" rel="shortcut icon">
   <title>Registro de Condominio</title>
   <link rel="icon" href="/img/ico_condo.ico">
</head>

<body>
   <img class="wave" src="img/wave.png">
   <div class="container">
      <div class="img">
         <img src="">
      </div>
      <div class="login-content">
         <form method="post" action="">
            <img src="/img/icono_condo.jpg">
            <h2 class="title">REGISTRARSE</h2>
            <?php
            // 1. Conexión y Lógica de Precarga
            $conexion = new mysqli("localhost", "root", "", "condominio");
            $conexion->set_charset("utf8");

            // Variables para controlar el estado
            $existe_condominio = false;
            $rif_defecto = "";
            $nombre_condo_defecto = "";
            $ubicacion_defecto = "";

            // Consultamos si ya existe al menos un administrador registrado
            // NOTA: Asegúrate que los nombres de columnas (rif_admin, etc) sean iguales en tu Base de Datos
            $sql = "SELECT rif_admin, nombre_condominio, direccion_condominio FROM administrador LIMIT 1";
            $resultado = $conexion->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
               $fila = $resultado->fetch_assoc();
               $existe_condominio = true;
               $rif_defecto = $fila['rif_admin'];
               $nombre_condo_defecto = $fila['nombre_condominio'];
               $ubicacion_defecto = $fila['direccion_condominio'];
            }

            include "controlador/controlador_register.php";
            // include "modelo/conexion.php"; // Comenté esto porque ya abriste conexión arriba y podría causar conflicto
            ?>

            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Usuario</h5>
                  <input type="text" class="input" name="usuario_admin" id="usuario_admin">
               </div>
            </div>

            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Nombre Completo del Encargado</h5>
                  <input type="text" class="input" name="nombre_completo_admin" id="nombre_completo_admin">
               </div>
            </div>

            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-envelope"></i>
               </div>
               <div class="div">
                  <h5>Correo Electronico</h5>
                  <input type="text" class="input" name="email_admin" id="email_admin">
               </div>
            </div>

            <div class="input-div pass">
               <div class="i">
                  <i class="fas fa-lock"></i>
               </div>
               <div class="div">
                  <h5>Contraseña</h5>
                  <input type="password" id="input" class="input" name="password_admin">
               </div>
            </div>
            <div class="view">
               <div class="fas fa-eye verPassword" onclick="vista()" id="verPassword"></div>
            </div>

            <div class="input-div one <?php if ($existe_condominio) echo 'focus'; ?>">
               <div class="i">
                  <i class="fas fa-id-card"></i>
               </div>

               <div class="div">
                  <h5>RIF del Condominio</h5>
                  <input type="text" class="input" name="rif_admin" id="rif_admin"
                     value="<?php echo $rif_defecto; ?>"
                     <?php if ($existe_condominio) echo 'readonly'; ?>>
               </div>
            </div>

            <div class="input-div one <?php if ($existe_condominio) echo 'focus'; ?>">
               <div class="i">
                  <i class="fas fa-building"></i>
               </div>
               <div class="div">
                  <h5>Nombre del Condominio</h5>
                  <input type="text" class="input" name="nombre_condominio" id="nombre_condominio"
                     value="<?php echo $nombre_condo_defecto; ?>"
                     <?php if ($existe_condominio) echo 'readonly'; ?>>
               </div>
            </div>

            <div class="input-div one <?php if ($existe_condominio) echo 'focus'; ?>">
               <div class="i">
                  <i class="fas fa-map-marker-alt"></i>
               </div>
               <div class="div">
                  <h5>Ubicacion del Condominio</h5>
                  <input type="text" class="input" name="direccion_condominio" id="direccion_condominio"
                     value="<?php echo $ubicacion_defecto; ?>"
                     <?php if ($existe_condominio) echo 'readonly'; ?>>
               </div>
            </div>

            <div class="text-center">
               <a class="font-italic isai5" href="login.php">¿Ya tienes una cuenta Creada?</a>
            </div>
            <input class="btn" type="submit" value="REGISTRAR CONDOMINIO" name="btnregistrar">
         </form>
      </div>
   </div>

   <script src="js/fontawesome.js"></script>
   <script src="js/main.js"></script>
   <script src="js/main2.js"></script>
   <script src="js/jquery.min.js"></script>
   <script src="js/bootstrap.js"></script>
   <script src="js/bootstrap.bundle.js"></script>

</body>

</html>