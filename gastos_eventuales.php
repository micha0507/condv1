<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/fondos.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add" />
    <title>Fondos</title>
</head>
<body>
    
    <?php include 'navbar.php'; ?>
<div class="contenido">

<div class="contenido">


    <h1>Registrar Gastos Eventuales</h1>
    <form action="./modelo/procesar_gasto.php" method="POST">
        <label for="concepto">Concepto:</label>
        <input type="text" id="concepto" name="concepto" required>
        
        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" required>
            <option value="Mantenimiento">Mantenimiento</option>
            <option value="Servicios Públicos">Servicios Públicos</option>
            <option value="Reparaciones">Reparaciones</option>
            <option value="Limpieza">Limpieza</option>
            <option value="Otros">Otros</option>
        </select>
        
        <label for="monto">Monto:</label>
        <input type="number" id="monto" name="monto" step="0.01" required>
        
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" max="<?php echo date('Y-m-d'); ?>" 
               min="<?php echo date('Y-m-01'); ?>" required>
        
        <button type="submit">Registrar Gasto</button>
    </form>
    </form>
    </div>
    </div>
</body>
</html>