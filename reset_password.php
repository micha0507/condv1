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
   <title>Restablecer Contraseña</title>
</head>

<body>
   <img class="wave" src="img/wave.png">
   <div class="container">
      <div class="img">
         <img src="img/bg.svg">
      </div>
      <div class="login-content">
         <form method="post" action="">
            <img src="img/avatar.svg">
            <h3 class="title">RECUPERAR CONTRASEÑA</h3>
            <?php
               include "reset_password/recovery.php";
               include "modelo/conexion.php";
            ?>
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Correo Electronico</h5>
                  <input id="usuario" type="text" class="input" name="email_propietario">
               </div>
            </div>
            <div class="text-center">
               <a class="font-italic isai5" href="login.php">¿Recordaste tu contraseña? Iniciar Sesion</a>
            </div>
            <input name="btnreset" class="btn" type="submit" value="RECUPERAR CONTRASEÑA">
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