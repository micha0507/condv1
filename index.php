<?php
include './modelo/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add" />
    <title>Panel Administrador</title>
</head>
<body>
<! -- Aquí va el código de la página principal del panel de administrador -->
    
        <?php include 'navbar.php'; ?>
     


    <! -- pantalla principal -->
    <div class="principal">

              
                
    <! -- ENCABEZADO PRINCIPAL -->
        <div class="encabezado_principal">
     <div class="caja_titulo">
                    <?php
                

                $sql = "SELECT rif, nombre, direccion FROM datos_condominio WHERE id = 1";
                $resultado = $conexion->query($sql); // Execute the query and assign the result to $resultado

                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    
                    echo  $fila['nombre'];
                    echo "<p><strong>". $fila['rif']."</strong> ";
                    
                } else {
                    echo "<p>No se encontraron datos para el condominio con ID 1.</p>";
                }

                
                ?>
     </div>   
    
</div>
        
    <! -- BARRA DE BUSQUEDA Y TITULO -->
        
            <h1 class="titulo">Estadísticas</h1>
          
            <! -- MARCO PARA LA BARRA DE BUSQUEDA -->
            
                    <div class="busqueda">

                    <input class="cuadro_busqueda" type="text" placeholder="Buscar último ">
                    <div class="botones_filtro">
                    <input type="radio" class="radios" name="filtro" id="nombre" value="nombre" >
                    <label for="nombre" class="radios">Nombre</label>
                   
                    <input type="radio" class="radios" name="filtro" id="nro_casa" value="nro_casa" >
                    <label class="radios" for="nombre">Nro. de Apto./Casa</label>
                    </div>
                    <a href="" class="enlace_filtro">
                    <div class="filtros">
                        <img src="./img/icon/filtros.png"alt="logo filtros" class="logo_filtros">
                        <p class="filtro">Filtros</p>
                    </div>
                    </a>
                </div>

            
        
        <! -- PANEL DE DE ADMINISTRACION  -->
        <div class="panel">
            
            <div class="first_row">
                <! -- PANEL PAGOS PENDIENTES  -->
                <a href="">
                <div class="marcos_panel">
                    <div class="para_pendientes">
                        <?php
                        // Consulta para contar los registros con status "Pendiente" y fecha de vencimiento menor a la fecha actual
                        $sql_morosos = "SELECT COUNT(*) AS total_pendientes 
                                        FROM facturas 
                                        WHERE status = 'Pendiente' 
                                        AND fecha_vencimiento >= (SELECT MAX(fecha_emision) FROM facturas)";
                        $resultado_morosos = $conexion->query($sql_morosos);

                        // Verificamos si hay resultados
                        if ($resultado_morosos->num_rows > 0) {
                            $fila_morosos = $resultado_morosos->fetch_assoc();
                            $total_pendientes = $fila_morosos['total_pendientes'];
                        } else {
                            $total_pendientes = 0;
                        }
                        ?>
                        <h1 class="numeros_morosos"><?php echo $total_pendientes; ?></h1>
                        <p>Pagos vencidos</p>
                    </div>
                </div>
                </a>
            
                <! -- PANEL ULTIMOS PAGOS TABLA  -->
                <?php include 'modelo/ultimos_pagos.php'; ?>
            </div> 
            <div id="publicaciones">            
               <h2>Publicaciones:</h2>
            <?php


                    // Consulta para obtener el último registro de la tabla 'post'
                    $sql = "SELECT contenido FROM post ORDER BY id_post DESC LIMIT 1";
                    $resultado = $conexion->query($sql);

                    // Verificamos si hay resultados
                    if ($resultado->num_rows > 0) {
                        // Obtenemos el contenido del último registro
                        $fila = $resultado->fetch_assoc();
                        echo $fila['contenido'];
                    } else {
                        echo "No hay registros en la tabla 'post'.";
                    }

                        ?>
                 
            </div>
            <?php
                // Consulta para obtener el último registro de la tabla 'factor'
                $sql = "SELECT factor, fecha FROM factor ORDER BY id DESC LIMIT 1";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    $ultimo_factor = $fila['factor'];
                    $ultima_fecha = $fila['fecha'];

                    echo "<p>Factor: $ultimo_factor Bs</p>";
                    echo "<p>Fecha: $ultima_fecha</p>";
                } else {
                    echo "<p>No hay registros en la tabla 'factor'.</p>";
                }
                ?>     
            </div>
           


        
           
                
            </div>
        </section>

    </div> </div>
</body>
</html>
