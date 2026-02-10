<?php
include './modelo/conexion.php';

session_start();
if (empty($_SESSION['id_admin'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}

// Obtener datos del administrador para el encabezado del PDF
$id_admin_sesion = $_SESSION['id_admin'];
$sql_admin_header = "SELECT usuario_admin, nombre_completo_admin, rif_admin, rol_admin, nombre_condominio, direccion_condominio FROM administrador WHERE id_admin = ?";
$stmt_admin = $conexion->prepare($sql_admin_header);
$stmt_admin->bind_param("i", $id_admin_sesion);
$stmt_admin->execute();
$data_admin = $stmt_admin->get_result()->fetch_assoc();

$rif = $nombre = $apellido = $usuario = $pass = $email_propietario = "";
$tipo_rif = "V";
$is_edit = false;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $tipo_rif = $_POST['tipo_rif'] ?? 'V';
        $rif_input = $_POST['rif'] ?? '';
        $rif = $tipo_rif . $rif_input;
        $nombre = $_POST['nombre'] ?? '';
        $apellido = $_POST['apellido'] ?? '';
        $usuario = $_POST['usuario'] ?? '';
        $pass = $_POST['pass'] ?? '';
        $email_propietario = $_POST['email_propietario'] ?? '';

        if (!preg_match("/^[a-zA-Z\s]+$/", trim($nombre))) {
            $message = "<div class='alert alert-danger' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>El nombre solo debe contener letras</div>";
        } elseif (!preg_match("/^[a-zA-ZñÑ\s]+$/", trim($apellido))) {
            $message = "<div class='alert alert-danger'style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>El apellido solo debe contener letras.</div>";
        } elseif ($action === 'add' && (strlen($pass) < 8 || strlen($pass) > 12)) {
            $message = "<div class='alert alert-danger' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>La contraseña debe tener entre 8 y 12 caracteres.</div>";
        } elseif ($action === 'edit' && $pass !== '' && (strlen($pass) < 8 || strlen($pass) > 12)) {
            $message = "<div class='alert alert-danger' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>La contraseña debe tener entre 8 y 12 caracteres.</div>";
        } elseif (!filter_var($email_propietario, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-danger' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>El email no es válido.</div>";
        } else {
            if ($action === 'edit') {
                $original_rif = $_POST['original_rif'] ?? $rif;
            } else {
                $original_rif = null;
            }

            $sql_check = "SELECT rif FROM propietario WHERE rif = ?";
            $stmt_check = $conexion->prepare($sql_check);
            $stmt_check->bind_param("s", $rif);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $exists = $result_check && $result_check->num_rows > 0;
            $allow_process = false;

            if ($action === 'add') {
                if ($exists) {
                    $message = "<div class='alert alert-warning' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>El RIF ya está registrado.</div>";
                } else {
                    $allow_process = true;
                }
            } else {
                if ($exists && $rif !== $original_rif) {
                    $message = "<div class='alert alert-warning' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>El RIF ya está registrado por otro propietario.</div>";
                } else {
                    $allow_process = true;
                }
            }

            if ($allow_process) {
                if ($action === 'add') {
                    $numero_residencia = $_POST['numero_residencia'] ?? '';
                    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
                    $conexion->begin_transaction();
                    try {
                        $sql = "INSERT INTO propietario (rif, nombre, apellido, usuario, pass, email_propietario) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("ssssss", $rif, $nombre, $apellido, $usuario, $pass_hashed, $email_propietario);
                        $stmt->execute();
                        $nuevo_id = $conexion->insert_id;

                        $sql_res = "INSERT INTO residencias (nro, id_propietario) VALUES (?, ?)";
                        $stmt_res = $conexion->prepare($sql_res);
                        $stmt_res->bind_param("si", $numero_residencia, $nuevo_id);
                        $stmt_res->execute();

                        $conexion->commit();
                        $message = "<div class='alert alert-success' style='background:linear-gradient(90deg,#e6ffed,#d4f7e2);border:1px solid #2ecc71;color:#155724;padding:12px 16px;border-radius:6px;font-weight:600;display:inline-block;'>Nuevo miembro y residencia añadidos exitosamente.</div>";
                        $rif = $nombre = $apellido = $usuario = $pass = $email_propietario = $numero_residencia = "";
                        $tipo_rif = "V";
                    } catch (Exception $e) {
                        $conexion->rollback();
                        $message = "<div class='alert alert-danger'>Error al registrar: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                } else {
                    // PROCESO DE EDICIÓN (CORREGIDO)
                    $numero_residencia = $_POST['numero_residencia'] ?? '';
                    $conexion->begin_transaction();
                    try {
                        // 1. Actualizar Propietario
                        if ($pass === '') {
                            $sql_up = "UPDATE propietario SET rif = ?, nombre = ?, apellido = ?, usuario = ?, email_propietario = ? WHERE rif = ?";
                            $stmt_up = $conexion->prepare($sql_up);
                            $stmt_up->bind_param("ssssss", $rif, $nombre, $apellido, $usuario, $email_propietario, $original_rif);
                        } else {
                            $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
                            $sql_up = "UPDATE propietario SET rif = ?, nombre = ?, apellido = ?, usuario = ?, pass = ?, email_propietario = ? WHERE rif = ?";
                            $stmt_up = $conexion->prepare($sql_up);
                            $stmt_up->bind_param("sssssss", $rif, $nombre, $apellido, $usuario, $pass_hashed, $email_propietario, $original_rif);
                        }
                        $stmt_up->execute();

                        // 2. Obtener ID del propietario
                        $res_id = $conexion->prepare("SELECT id FROM propietario WHERE rif = ?");
                        $res_id->bind_param("s", $rif);
                        $res_id->execute();
                        $id_propietario_edit = $res_id->get_result()->fetch_assoc()['id'];

                        // 3. ACTUALIZACIÓN DE RESIDENCIA (CORRECCIÓN DE DUPLICIDAD)
                        // Verificamos si ya existe una residencia para este ID de propietario
                        $check_res = $conexion->prepare("SELECT id FROM residencias WHERE id_propietario = ?");
                        $check_res->bind_param("i", $id_propietario_edit);
                        $check_res->execute();
                        $res_check_exist = $check_res->get_result();

                        if ($res_check_exist->num_rows > 0) {
                            // Si existe, actualizamos el número
                            $sql_res_up = "UPDATE residencias SET nro = ? WHERE id_propietario = ?";
                            $stmt_res_up = $conexion->prepare($sql_res_up);
                            $stmt_res_up->bind_param("si", $numero_residencia, $id_propietario_edit);
                        } else {
                            // Si por alguna razón no existía, la creamos
                            $sql_res_up = "INSERT INTO residencias (nro, id_propietario) VALUES (?, ?)";
                            $stmt_res_up = $conexion->prepare($sql_res_up);
                            $stmt_res_up->bind_param("si", $numero_residencia, $id_propietario_edit);
                        }
                        $stmt_res_up->execute();

                        $conexion->commit();
                        $message = "<div class='alert alert-success' style='background:linear-gradient(90deg,#e6ffed,#d4f7e2);border:1px solid #2ecc71;color:#155724;padding:12px 16px;border-radius:6px;font-weight:600;display:inline-block;'>Datos actualizados correctamente.</div>";
                        $rif = $nombre = $apellido = $usuario = $pass = $email_propietario = $numero_residencia = "";
                        $tipo_rif = "V";
                        $is_edit = false;
                    } catch (Exception $e) {
                        $conexion->rollback();
                        $message = "<div class='alert alert-danger'>Error al actualizar: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                }
            }
            $stmt_check->close();
        }
    }

    // Eliminar miembro
    if (!empty($_POST['action']) && $_POST['action'] === 'delete' && !empty($_POST['delete_rif'])) {
        $del_rif = $_POST['delete_rif'];
        $sql_del = "DELETE FROM propietario WHERE rif = ?";
        $stmt_del = $conexion->prepare($sql_del);
        $stmt_del->bind_param("s", $del_rif);
        if ($stmt_del->execute()) {
            $message = "<div class='alert alert-success' style='border:1px solid #ec0a0a;color:#ec0a0a;padding:6px 13px;border-radius:6px;font-weight:600;display:inline-block;'>Propietario eliminado.</div>";
        }
        $stmt_del->close();
    }
} elseif (!empty($_GET['edit_rif'])) {
    $edit_rif = $_GET['edit_rif'];
    $sql_get = "SELECT p.*, r.nro FROM propietario p LEFT JOIN residencias r ON p.id = r.id_propietario WHERE p.rif = ?";
    $stmt_get = $conexion->prepare($sql_get);
    $stmt_get->bind_param("s", $edit_rif);
    $stmt_get->execute();
    $res_get = $stmt_get->get_result();

    if ($res_get && $res_get->num_rows > 0) {
        $row = $res_get->fetch_assoc();
        $full_rif = $row['rif'];
        $tipo_rif = substr($full_rif, 0, 1);
        $rif = substr($full_rif, 1);
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $usuario = $row['usuario'];
        $email_propietario = $row['email_propietario'];
        $numero_residencia = $row['nro'] ?? '';
        $is_edit = true;
    }
    $stmt_get->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="./css/propietarios.css">
    <link rel="stylesheet" href="./css/carga_masiva.css">
    <link rel="stylesheet" href="./css/pagos.css">
    <style>
        #header-reporte {
            display: none;
        }

        @media print {
            @page {
                size: letter;
                margin: 1.5cm;
            }

            /* Ocultar todo por defecto y mostrar solo header + tabla para impresión */
            body * {
                visibility: hidden;
            }

            #header-reporte,
            #header-reporte *,
            .tabla-propietarios,
            .tabla-propietarios * {
                visibility: visible;
            }

            /* Encabezado similar a index_admin.php */
            #header-reporte {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 2px solid #2c3e50;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }

            .reporte-wrapper {
                display: flex;
                width: 100%;
                gap: 20px;
                align-items: center;
            }

            .reporte-logo img {
                width: 120px;
                height: auto;
                border-radius: 8px;
            }

            .reporte-datos {
                font-size: 12px;
                color: #000;
                line-height: 1.4;
            }

            .reporte-datos h2 {
                margin: 0;
                font-size: 18px;
                color: #2c3e50;
            }


            .tabla-propietarios {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 140px;
                /* dejar espacio para el header */
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }

            .tabla-propietarios thead th {
                background: #2c3e50 !important;
                color: #fff !important;
                padding: 100px !important;
                text-align: left !important;
                border: 1px solid #ccc !important;
            }

            .tabla-propietarios td {
                padding: 10px !important;
                border-bottom: 1px solid #eee !important;
                color: #000 !important;
                vertical-align: middle !important;
            }

            .tabla-propietarios tr:nth-child(even) td {
                background: #fcfcfc !important;
            }

            /* Ocultar controles y elementos no relevantes */
            .btn_imprimir,
            button,
            .busqueda-realtime,
            .caja_izq,
            .tabla-header-actions,
            a,
            .paginador {
                display: none !important;
            }

            body {
                background: #fff;
                font-family: Arial, sans-serif;
                color: #000;
            }
        }

        /* Contenedor principal para dividir en 2 columnas */
        .contenedor-miembros {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-top: 20px;
        }

        /* Columna izquierda: Formulario */
        .caja_izq {
            flex: 1;
            /* Ocupa menos espacio */
            min-width: 350px;
            position: sticky;
            top: 20px;
        }

        /* Columna derecha: Listado */
        .caja_der {
            flex: 2;
            /* Ocupa más espacio */
        }

        /* Barra de búsqueda */
        .busqueda-realtime {
            margin-bottom: 15px;
            position: relative;
        }

        .busqueda-realtime input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        .busqueda-realtime .material-symbols-outlined {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        /* Ajustes para móviles */
        @media (max-width: 1000px) {
            .contenedor-miembros {
                flex-direction: column;
            }

            .caja_izq,
            .caja_der {
                width: 100%;
                flex: none;
                position: static;
            }
        }
    </style>
    <meta charset="UTF-8">
    <title>Añadir / Editar Miembro</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="principal">

        <?php if (!empty($message)) echo $message; ?>

        <div class="contenedor-miembros">

            <div class="caja_izq carga_pago">
                <h2 style="margin-bottom:20px; color: #2c3e50;"><?php echo $is_edit ? 'Editar Miembro' : 'Añadir Miembro'; ?></h2>

                <form id="anadirMiembroForm" method="post" action="anadir_miembro.php<?= $is_edit ? '?edit_rif=' . urlencode(($tipo_rif . $rif)) : '' ?>">
                    <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'add' ?>">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="original_rif" value="<?php echo htmlspecialchars($tipo_rif . $rif); ?>">
                    <?php endif; ?>

                    <label for="rif">RIF:</label>
                    <div style="display: flex; gap: 5px; margin-bottom: 10px;">
                        <select id="tipo_rif" name="tipo_rif" required style="width: 30%;">
                            <option value="V" <?= $tipo_rif === 'V' ? 'selected' : '' ?>>V</option>
                            <option value="J" <?= $tipo_rif === 'J' ? 'selected' : '' ?>>J</option>
                            <option value="G" <?= $tipo_rif === 'G' ? 'selected' : '' ?>>G</option>
                            <option value="E" <?= $tipo_rif === 'E' ? 'selected' : '' ?>>E</option>
                            <option value="C" <?= $tipo_rif === 'C' ? 'selected' : '' ?>>C</option>
                        </select>
                        <input type="text" id="rif" placeholder="Ej: 12345678" name="rif" value="<?php echo htmlspecialchars($rif); ?>" required style="flex: 1;">
                    </div>

                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan" value="<?php echo htmlspecialchars($nombre); ?>" required><br>

                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Ej: Pérez" value="<?php echo htmlspecialchars($apellido); ?>" required><br>

                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Ej: juanperez123" value="<?php echo htmlspecialchars($usuario); ?>" required><br>

                    <label for="pass">Contraseña:</label>
                    <input type="password" id="pass" name="pass" placeholder="Entre 8 y 12 caracteres" value="<?php echo htmlspecialchars($pass); ?>"><br>

                    <label for="email_propietario">Email:</label>
                    <input type="email" id="email_propietario" name="email_propietario" placeholder="Ej: juan@email.com" value="<?php echo htmlspecialchars($email_propietario); ?>" required><br>

                    <label for="numero_residencia">Número de Residencia:</label>
                    <input type="text" id="numero_residencia" name="numero_residencia" placeholder="Ej: 101" value="<?php echo isset($numero_residencia) ? htmlspecialchars($numero_residencia) : ''; ?>" required><br>

                    <button type="submit" style="width: 100%;"><?php echo $is_edit ? 'Guardar Cambios' : 'Registrar Propietario'; ?></button><br><br>

                    <?php if ($is_edit): ?>
                        <a href="anadir_miembro.php" style='display:block; text-align:center; margin-top:10px; background:#c0392b; padding: 10px; border-radius: 5px; color: white; text-decoration: none;'>Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="caja_der carga_pago">

                <div id="header-reporte">
                    <div class="reporte-wrapper">
                        <div class="reporte-logo">
                            <img src="./img/icono_condo.jpg" alt="Logo">
                        </div>
                        <div class="reporte-datos">
                            <h2><?php echo htmlspecialchars($data_admin['nombre_condominio']); ?></h2>
                            <p><strong>RIF:</strong> <?php echo htmlspecialchars($data_admin['rif_admin']); ?></p>
                            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($data_admin['direccion_condominio']); ?></p>
                            <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($data_admin['nombre_completo_admin']); ?> (<?php echo htmlspecialchars($data_admin['rol_admin']); ?>)</p>
                            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($data_admin['usuario_admin']); ?>
                            <p style="margin: 2px 0;"><strong>Fecha de Reporte:</strong> <?php
                                                                                            date_default_timezone_set('America/Caracas');
                                                                                            echo date('d/m/Y h:i A');
                                                                                            ?>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <span style="font-size: 14px; font-weight: bold; text-transform: uppercase; color:#2c3e50;">Registro Propietarios</span>
                    </div>
                </div>

                <div class="tabla-header-actions">
                    <h2 style="margin-bottom:20px; color: #2c3e50;">Propietarios Registrados</h2>
                </div>

                <div class="busqueda-realtime">
                    <span class="material-symbols-outlined"></span>
                    <input type="text" id="inputBusqueda" placeholder="Buscar ">
                </div>

                <?php
                // --- LÓGICA DE REGISTROS CON BÚSQUEDA ---
                $per_page = 9;
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $offset = ($page - 1) * $per_page;

                // Obtener término de búsqueda
                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $search_param = "%$search%";

                // Contar total con filtro
                $sql_count = "SELECT COUNT(*) AS total FROM propietario p 
              LEFT JOIN residencias r ON p.id = r.id_propietario 
              WHERE p.rif LIKE ? OR p.nombre LIKE ? OR p.apellido LIKE ? OR p.usuario LIKE ? OR r.nro LIKE ?";
                $stmt_c = $conexion->prepare($sql_count);
                $stmt_c->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
                $stmt_c->execute();
                $total_rows = $stmt_c->get_result()->fetch_assoc()['total'];
                $total_pages = ceil($total_rows / $per_page);

                // Consulta principal filtrada
                $sql_list = "SELECT p.rif, p.nombre, p.apellido, p.usuario, p.email_propietario, r.nro AS residencia 
             FROM propietario p 
             LEFT JOIN residencias r ON p.id = r.id_propietario 
             WHERE p.rif LIKE ? OR p.nombre LIKE ? OR p.apellido LIKE ? OR p.usuario LIKE ? OR r.nro LIKE ?
             ORDER BY p.id DESC LIMIT ?, ?";

                $stmt_list = $conexion->prepare($sql_list);
                $stmt_list->bind_param("sssssii", $search_param, $search_param, $search_param, $search_param, $search_param, $offset, $per_page);
                $stmt_list->execute();
                $result_list = $stmt_list->get_result();

                if ($result_list && $result_list->num_rows > 0) {
                    echo '<div class="table-responsive">
                                <table class="tabla-propietarios" id="tablaMiembros">
                                    <thead>
                                        <tr>
                                            <th>RIF</th>
                                            <th>Nombre y Apellido</th>
                                            <th>Residencia</th>
                                            <th>Usuario</th>
                                            <th class="acciones-col">Acciones</th> 
                                        </tr>
                                    </thead>
                                    <tbody>';

                    while ($row = $result_list->fetch_assoc()) {
                        $safe_rif = htmlspecialchars($row['rif']);
                        $safe_full_name = htmlspecialchars($row['nombre'] . " " . $row['apellido']);
                        $safe_residencia = htmlspecialchars($row['residencia'] ?? 'N/A');
                        $safe_usuario = htmlspecialchars($row['usuario']);
                        $edit_url = "anadir_miembro.php?edit_rif=" . urlencode($row['rif']) . "&page=" . $page;

                        echo "<tr>
                                    <td>{$safe_rif}</td>
                                    <td>{$safe_full_name}</td>
                                    <td><strong>{$safe_residencia}</strong></td> 
                                    <td>{$safe_usuario}</td>
                                    <td class='acciones-col'>
                                        <div style='display:flex; gap:5px;'>
                                            <a href='{$edit_url}' class='btn-edit'>Editar</a>
                                            <form method='post' style='margin:0;' onsubmit=\"return confirm('¿Eliminar propietario {$safe_full_name}?');\">
                                                <input type='hidden' name='action' value='delete'>
                                                <input type='hidden' name='delete_rif' value='{$safe_rif}'>
                                                <button type='submit' class='delete-row' style='background:#e74c3c; padding: 10px; border-radius: 6px; font-size:13px; width:100%;' >Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                  </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p style='text-align:center; padding:20px;'>No hay miembros registrados.</p>";
                }
                $stmt_list->close(); {

                    // --- PAGINADOR ACTUALIZADO ---
                    if ($total_pages > 1) {
                        echo '<div class="paginador" style="margin:20px 0;text-align:center;">';

                        // Crear base de la URL conservando la búsqueda
                        $base_url = "anadir_miembro.php?search=" . urlencode($search);

                        if ($page > 1) {
                            echo '<a href="' . $base_url . '&page=' . ($page - 1) . '" style="margin:0 5px;padding:6px 12px;border-radius:4px;background:#eee;color:#333;text-decoration:none;">&laquo; Anterior</a>';
                        }

                        for ($i = 1; $i <= $total_pages; $i++) {
                            $active_style = $i == $page ? 'background:#4caf50;color:#fff;' : 'background:#eee;color:#333;';
                            echo '<a href="' . $base_url . '&page=' . $i . '" style="margin:0 2px;padding:6px 12px;border-radius:4px;' . $active_style . 'text-decoration:none;">' . $i . '</a>';
                        }

                        if ($page < $total_pages) {
                            echo '<a href="' . $base_url . '&page=' . ($page + 1) . '" style="margin:0 5px;padding:6px 12px;border-radius:4px;background:#eee;color:#333;text-decoration:none;">Siguiente &raquo;</a>';
                        }
                        echo '</div>';
                    }
                }

                ?>
                <button type="button" id="btnShowPrintModal" class="btn-print" style="background-color: #e74c3c; color: white; padding: 10px; border-radius: 5px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined "></span> Guardar PDF / Imprimir
                </button>
            </div>
        </div>
    </section>

    <div id="modalFondo" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;"></div>
    <div id="modalPrint" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 1000; text-align: center; width: 350px;">

        <h2 style="margin: 0 0 10px 0; color: #4CAF50;">Listado Generado Exitosamente</h2>
        <p style="color: #666; font-size: 14px;">¿Deseas emitir el PDF del listado del personal?</p>
        <div style="margin-top: 25px; display: flex; justify-content: center; gap: 10px;">
            <button id="confirmarPrint" style="padding: 10px 20px; background-color: #1ecaf5; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir</button>
            <button id="cancelarPrint" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancelar</button>
        </div>
    </div>

    <script>
        let timeout = null;
        document.getElementById('inputBusqueda').addEventListener('keyup', function() {
            clearTimeout(timeout);
            let valor = this.value;

            // Esperar 500ms después de escribir para no saturar el servidor
            timeout = setTimeout(function() {
                // Redirigir a la misma página con el parámetro de búsqueda
                window.location.href = 'anadir_miembro.php?search=' + encodeURIComponent(valor);
            }, 500);
        });

        // Mantener el foco y el valor en el input después de recargar
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchVal = urlParams.get('search');
            if (searchVal) {
                const input = document.getElementById('inputBusqueda');
                input.value = searchVal;
                input.focus();
                // Poner el cursor al final del texto
                input.setSelectionRange(input.value.length, input.value.length);
            }
        };

        $(document).ready(function() {
            // Abrir el modal de impresión
            $('#btnShowPrintModal').on('click', function() {
                $('#modalFondo').fadeIn();
                $('#modalPrint').fadeIn();
            });

            // Cerrar el modal
            $('#cancelarPrint, #modalFondo').on('click', function() {
                $('#modalFondo').fadeOut();
                $('#modalPrint').fadeOut();
            });

            // Confirmar y redirigir
            $('#confirmarPrint').on('click', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const search = urlParams.get('search') || '';

                // Abrir el nuevo comprobante en una pestaña nueva pasando el filtro de búsqueda
                window.open('modelo/comprobante_listadopropietario.php?search=' + encodeURIComponent(search), '_blank');

                // Cerrar modal
                $('#modalFondo').fadeOut();
                $('#modalPrint').fadeOut();
            });
        });
    </script>

    <style>
        /* Mantener estructura y anchos de columna al filtrar */
        .tabla-propietarios {
            table-layout: fixed;
            width: 100%;
        }

        .tabla-propietarios th,
        .tabla-propietarios td {
            word-break: break-word;
            overflow-wrap: anywhere;
        }
    </style>
</body>

</html>