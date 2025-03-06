<?php 
  
if(!empty($_POST["btnregistrar"])){
    if(empty($_POST["usuario_admin"]) or empty($_POST["nombre_completo_admin"]) or empty($_POST["rif_admin"]) or empty($_POST["email_admin"]) or empty($_POST["password_admin"])){
        echo "<div class='alert alert-danger'>Uno de los campos está vacios</div>";
    
    }else{

    /*Almacenamiento de Datos ingresados a Base de datos */

        $usuario_admin=$_POST["usuario_admin"];
        $nombre_completo_admin=$_POST["nombre_completo_admin"];
        $rif_admin=$_POST["rif_admin"];
        $email_admin=$_POST["email_admin"];
        $password_admin=$_POST["password_admin"];

        /* Guardar datos ingresados en las tablas */

        $sql=$conexion->query("INSERT INTO administrador (usuario_admin, nombre_completo_admin, rif_admin, email_admin, password_admin) 
        VALUES ('$usuario_admin', '$nombre_completo_admin', '$rif_admin', '$email_admin', '$password_admin')");

       /* Variable para que funcione el $query, es decir, poder guardar los datos ingresados en el registro */

        if($sql){

            echo "<div class='alert alert-success'>Registro de Condominio Exitoso $usuario_admin</div>";
            echo "<a href='login.php'>Iniciar Sesión</a>";
        }else{
            echo "<div class='alert alert-danger'>Error al Registrar</div>";
        }
    }
}   
?>