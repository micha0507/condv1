<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <link rel="stylesheet" href="../css/bootstrap.css">
   <link rel="stylesheet" type="text/css" href="../css/style.css">
   <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
   <title>Restablecer Contraseña</title>
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
            include "reset_password/recovery.php";
            include "modelo/conexion.php";
            ?>
            <div class="input-div one">
               <div class="i">
                  <i class="fas fa-user"></i>
               </div>
               <div class="div">
                  <h5>Correo Electronico</h5>
                  <input id="usuario" type="text" class="input" name="email_admin">
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