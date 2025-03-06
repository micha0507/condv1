<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <!-- <link rel="stylesheet" href="css/all.min.css"> -->
   <!-- <link rel="stylesheet" href="css/fontawesome.min.css"> -->
   <link href="https://tresplazas.com/web/img/big_punto_de_venta.png" rel="shortcut icon">
   <title>Registro de Condominio</title>
</head>

<body>
   <img class="wave" src="img/wave.png">
   <div class="container">
      <div class="img">
         <img src="img/bg.svg">
      </div>
      <div class="login-content">
         <form action="" method="post">
            <img src="img/avatar.svg">
            <h2 class="title">REGISTRARSE</h2>
            <?php
               $conexion=new mysqli("localhost","root","","condominio");
               $conexion->set_charset("utf8");
               include "controlador/controlador_register.php";
               include "modelo/conexion.php";
            ?>
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Usuario</h5>
                  <input type="text" class="input" name="usuario_admin" id="usuario_admin" >
               </div>
            </div>

            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Nombre Completo del Encargado</h5>
                  <input type="text" class="input" name="nombre_completo_admin" id="nombre_completo_admin" >
               </div>
            </div>
            
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>RIF del Condominio</h5>
                  <input type="text" class="input" name="rif_admin" id="rif_admin">
               </div>
            </div>

            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
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