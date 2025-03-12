<?php

session_start();
if(empty($_SESSION["id_admin"])){
	header("location: login.php");
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
	<div class="bd-example mb-0" style="height: 80vh">
		<div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
				<li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
				<li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
				<li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner">
				<div class="carousel-item active" style="height: 80vh">
					<img src="img/1.jpg" class="d-block w-100" alt="...">
					<div class="carousel-caption d-none d-md-block">
						<h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP 4</h5>
						<p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
					</div>
				</div>
				<div class="carousel-item" style="height: 80vh">
					<img src="img/1.jpg" class="d-block w-100" alt="...">
					<div class="carousel-caption d-none d-md-block">
						<h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP 4</h5>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
					</div>
				</div>
				<div class="carousel-item" style="height: 80vh">
					<img src="img/1.jpg" class="d-block w-100" alt="...">
					<div class="carousel-caption d-none d-md-block">
						<h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP 4</h5>
						<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
					</div>
				</div>
			</div>
			<a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	</div>

	<nav class="navbar navbar-dark bg-dark  navbar-expand-md navbar-light bg-light fixed-top">
		<div class="text-white bg-success p-2">
			<?php
			    echo $_SESSION["rol_admin"]."\n". $_SESSION["usuario_admin"];
			?>
		</div>
		<div class="collapse navbar-collapse" id="navbarTogglerDemo01">
			<div class="navbar-nav mr-auto">
				<div class="offset-md-1 mr-auto text-center"></div>
				<a class="nav-item nav-link text-justify active ml-3 hover-primary" href="#">Inicio</a>
				<a class="nav-item nav-link text-justify ml-3 hover-primary" href="#">Nosotros</a>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle text-justify ml-3" href="" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Servicios
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
						<a class="dropdown-item" href="#">Preguntas Frecuentes</a>
						<a class="dropdown-item" href="#">Compras</a>
						<a class="dropdown-item" href="servicios.html">Otros</a>
					</div>
				</li>
				<a class="nav-item nav-link text-justify ml-3 hover-primary" href="controlador/controlador_cerrar_sesion.php">Salir</a>
			</div>
			<div class="text-center justify-content-center">
				<a class="btn btn-outline-primary" target="_blank" href="https://www.facebook.com">Facebook</a>
				<a class="btn btn-outline-danger" target="_blank" href="https://www.youtube.com">Youtube</a>
			</div>
		</div>

	</nav>
	<div class="">
		<div class="jumbotron bg-dark text-light rounded-0">
			<h1 class="display-4">Hello, world!</h1>
			<p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
			<hr class="my-4 bg-light">
			<div class="d-flex justify-content-between align-items-center flex-wrap">
				<p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
				<a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
			</div>
		</div>
	</div>

	<form action="" class="form-inline d-flex justify-content-center flex-column flex-md-row">
		<div class="form-group mx-2 my-2">
			<label class="d-none d-md-block" for="">Nombre</label>
			<input type="text" class="form-control" placeholder="Nombre">
		</div>
		<div class="form-group mx-2 my-2">
			<label class="d-none d-md-block" for="">Apellido</label>
			<input type="text" class="form-control" placeholder="Apellido">
		</div>
		<div class="form-group mx-2 my-2">
			<button class="btn btn-outline-primary">enviar</button>
		</div>
	</form>
	<script src="js/jquery-3.3.1.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>

</html>