<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener el mes y año actual
$mes_actual = date('m');
$anio_actual = date('Y');

// Consultar la suma de los montos de la tabla gastos_eventuales para el mes actual
$sql_gastos = "SELECT SUM(monto) AS total_gastos 
               FROM gastos_eventuales 
               WHERE MONTH(fecha) = '$mes_actual' AND YEAR(fecha) = '$anio_actual'";
$resultado_gastos = $conexion->query($sql_gastos);
$total_gastos = 0;

if ($resultado_gastos && $fila = $resultado_gastos->fetch_assoc()) {
    $total_gastos = $fila['total_gastos'] ?? 0;
}

// Consultar la suma de los montos de la tabla pagos para el mes actual con status "Validado"
$sql_pagos = "SELECT SUM(monto) AS total_pagos 
              FROM pagos 
              WHERE MONTH(fecha) = '$mes_actual' AND YEAR(fecha) = '$anio_actual' AND status = 'Validado'";
$resultado_pagos = $conexion->query($sql_pagos);
$total_pagos = 0;

if ($resultado_pagos && $fila = $resultado_pagos->fetch_assoc()) {
    $total_pagos = $fila['total_pagos'] ?? 0;
}

// Calcular la diferencia entre los pagos y los gastos
$diferencia = $total_pagos - $total_gastos;

// Mostrar los resultados con los modales corregidos
echo '<div id="pagos" onclick="abrirModalPagos()" class="marcos"><p>Total pagos:<br>Bs <b>' . number_format($total_pagos, 2) . '</b></p></div>';
echo '<div id="gastos" onclick="abrirModalGastos()" class="marcos"><p>Total gastos:<br><b> Bs ' . number_format($total_gastos, 2) . '</b></p></div>';
echo '<div class=""><p>Fondos disponibles:</p> <b><h2 id="fondos-disponibles">Bs 0.00</h2></b></div>';
echo "
<script>
document.addEventListener('DOMContentLoaded', function() {
    var target = " . json_encode($diferencia) . ";
    var duration = 1200;
    var start = 0;
    var startTime = null;
    var el = document.getElementById('fondos-disponibles');
    function animateFondos(ts) {
        if (!startTime) startTime = ts;
        var progress = Math.min((ts - startTime) / duration, 1);
        var current = start + (target - start) * progress;
        el.textContent = 'Bs ' + current.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (progress < 1) {
            requestAnimationFrame(animateFondos);
        }
    }
    requestAnimationFrame(animateFondos);
});
</script>
";

// Cerrar la conexión
$conexion->close();
?>