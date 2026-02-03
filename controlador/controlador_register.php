<?php 

if(!empty($_POST["btnregistrar"])){
    // 1. Añadimos la validación para que no esté vacío
    if(
        empty($_POST["usuario_admin"]) || 
        empty($_POST["nombre_completo_admin"]) || 
        empty($_POST["rif_admin"]) || 
        empty($_POST["email_admin"]) || 
        empty($_POST["password_admin"]) ||
        empty($_POST["nombre_condominio"]) ||
        empty($_POST["direccion_condominio"]) 
    ){
        echo "<div class='alert alert-danger'>Uno de los campos está vacios</div>";
    }else{

        $usuario_admin = $_POST["usuario_admin"];
        $nombre_completo_admin = $_POST["nombre_completo_admin"];
        $rif_admin = $_POST["rif_admin"];
        $email_admin = $_POST["email_admin"];
        $password_admin = $_POST["password_admin"];
        $nombre_condominio = $_POST["nombre_condominio"];
        $direccion_condominio = $_POST["direccion_condominio"]; 

        $password_hash = password_hash($password_admin, PASSWORD_DEFAULT);

        // 2. Agregamos la data
        $stmt = $conexion->prepare(
            "INSERT INTO administrador (usuario_admin, nombre_completo_admin, rif_admin, email_admin, password_admin, nombre_condominio, direccion_condominio) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        // 3. Agregamos una "s" adicional y la variable al final
        $stmt->bind_param(
            "sssssss", 
            $usuario_admin, 
            $nombre_completo_admin, 
            $rif_admin, 
            $email_admin, 
            $password_hash, 
            $nombre_condominio,
            $direccion_condominio
        );

        if($stmt->execute()){
            echo "<div class='alert alert-success'>Registro de Condominio Exitoso $usuario_admin</div>";
            echo "<a href='login.php'>Iniciar Sesión</a>";
        }else{
            echo "<div class='alert alert-danger'>Error al Registrar</div>";
        }
        $stmt->close();
    }
}   
?>