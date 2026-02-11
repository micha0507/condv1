<?php
session_start();

/**
 * Validamos quién está pidiendo cerrar sesión.
 * Usamos un parámetro por URL (?rol=admin o ?rol=propietario) 
 * para mayor precisión, o detectamos las variables activas.
 */

$rol = isset($_GET['rol']) ? $_GET['rol'] : '';

if ($rol == 'Administrador') {
    // Solo borramos lo relacionado al Administrador
    unset($_SESSION['id_admin']);
    unset($_SESSION['nombre_admin']);
    // No usamos session_destroy() para no afectar al propietario
} 
elseif ($rol == 'Propietario') {
    // Solo borramos lo relacionado al Propietario
    unset($_SESSION['id']);
    unset($_SESSION['id_propietario']);
    unset($_SESSION['nombre_propietario']);
} 
else {
    // Si no se especifica rol, por seguridad verificamos qué existe y borramos selectivamente
    if (isset($_SESSION['id_admin']) && !isset($_SESSION['id'])) {
        unset($_SESSION['id_admin']);
    } elseif (isset($_SESSION['id']) && !isset($_SESSION['id_admin'])) {
        unset($_SESSION['id']);
        unset($_SESSION['id_propietario']);
    }
}

// Redirección al login único
header("Location: ../login.php");
exit;