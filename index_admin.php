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
        <div class="encabezado_principal"></div>
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
        <section class="panel">
            
            <div class="first_row">
                <! -- PANEL PAGOS VENCIDOS  -->
                <a href="">
                <div class="marcos_panel">
                    <div class="para_morosos">
                        <h1 class="numeros_morosos">18</h4>
                        <p>Pagos vencidos</p>
                    </div>
                </div>
                </a> 
                <! -- PANEL ULTIMOS PAGOS TABLA  -->
            <?php include 'modelo/ultimos_pagos.php'; ?>
        </section>
    </div>
</body>
</html>