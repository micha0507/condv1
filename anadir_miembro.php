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



$rif = $nombre = $apellido = $usuario = $pass = $email_propietario = "";
$tipo_rif = "V";
$is_edit = false;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Acciones desde el formulario
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

        // Validaciones
        if (!preg_match("/^[a-zA-Z\s]+$/", trim($nombre))) {
            $message = "<div class='alert alert-danger'>El nombre solo debe contener letras</div>";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", trim($apellido))) {
            $message = "<div class='alert alert-danger'>El apellido solo debe contener letras.</div>";
        } elseif ($action === 'add' && (strlen($pass) < 8 || strlen($pass) > 12)) {
            $message = "<div class='alert alert-danger'>La contraseña debe tener entre 8 y 12 caracteres.</div>";
        } elseif ($action === 'edit' && $pass !== '' && (strlen($pass) < 8 || strlen($pass) > 12)) {
            $message = "<div class='alert alert-danger'>La contraseña debe tener entre 8 y 12 caracteres.</div>";
        } elseif (!filter_var($email_propietario, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-danger'>El email no es válido.</div>";
        } else {
            // Si se está editando, obtengo el rif original
            if ($action === 'edit') {
                $original_rif = $_POST['original_rif'] ?? $rif;
            } else {
                $original_rif = null;
            }

            // Comprobar si el RIF ya existe (si es add o si cambió el rif en edit)
            $sql_check = "SELECT rif FROM propietario WHERE rif = ?";
            $stmt_check = $conexion->prepare($sql_check);
            $stmt_check->bind_param("s", $rif);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            $exists = $result_check && $result_check->num_rows > 0;
            $allow_insert = false;

            if ($action === 'add') {
                if ($exists) {
                    $message = "<div class='alert alert-warning'>El RIF ya está registrado.</div>";
                } else {
                    $allow_insert = true;
                }
            } else {
                // si existe y no es el mismo registro que estamos editando -> conflicto
                if ($exists && $rif !== $original_rif) {
                    $message = "<div class='alert alert-warning'>El RIF ya está registrado por otro propietario.</div>";
                } else {
                    $allow_insert = true;
                }
            }

            if ($allow_insert) {
                if ($action === 'add') {
                    // CAPTURAR EL NÚMERO DE RESIDENCIA
                    $numero_residencia = $_POST['numero_residencia'] ?? '';

                    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);

                    // Iniciar una transacción para asegurar que se guarden ambos o ninguno
                    $conexion->begin_transaction();

                    try {
                        // 1. Insertar en Propietario
                        $sql = "INSERT INTO propietario (rif, nombre, apellido, usuario, pass, email_propietario) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $conexion->prepare($sql);
                        $stmt->bind_param("ssssss", $rif, $nombre, $apellido, $usuario, $pass_hashed, $email_propietario);
                        $stmt->execute();

                        // --- CORRECCIÓN AQUÍ ---
                        // Obtenemos el ID automático que MySQL le asignó al propietario arriba
                        $nuevo_id = $conexion->insert_id;

                        // 2. Insertar en Residencias usando el ID numérico
                        $sql_res = "INSERT INTO residencias (nro, id_propietario) VALUES (?, ?)";
                        $stmt_res = $conexion->prepare($sql_res);
                        // "si" significa: primer parámetro es string (nro), segundo es integer (id)
                        $stmt_res->bind_param("si", $numero_residencia, $nuevo_id);
                        $stmt_res->execute();
                        $stmt_res->close();
                        // -----------------------

                        $conexion->commit();
                        $message = "<div class='alert alert-success'>Nuevo miembro y residencia añadidos exitosamente.</div>";

                        // Limpiar variables
                        $rif = $nombre = $apellido = $usuario = $pass = $email_propietario = $numero_residencia = "";
                        $tipo_rif = "V";
                    } catch (Exception $e) {
                        // Si algo falla, revertimos
                        $conexion->rollback();
                        $message = "<div class='alert alert-danger'>Error al registrar: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    $stmt->close();
                } else { // update
                    $numero_residencia = $_POST['numero_residencia'] ?? '';

                    // Iniciamos transacción para asegurar que ambos cambios ocurran
                    $conexion->begin_transaction();

                    try {
                        // 1. Actualizar Propietario usando el ORIGINAL_RIF para encontrarlo
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

                        // 2. Obtener el ID interno del propietario (ya que el RIF pudo haber cambiado)
                        $res_id = $conexion->prepare("SELECT id FROM propietario WHERE rif = ?");
                        $res_id->bind_param("s", $rif);
                        $res_id->execute();
                        $id_propietario_edit = $res_id->get_result()->fetch_assoc()['id'];

                        // 3. Actualizar o Insertar Residencia
                        $sql_res_up = "INSERT INTO residencias (nro, id_propietario) VALUES (?, ?) 
                       ON DUPLICATE KEY UPDATE nro = VALUES(nro)";
                        $stmt_res_up = $conexion->prepare($sql_res_up);
                        $stmt_res_up->bind_param("si", $numero_residencia, $id_propietario_edit);
                        $stmt_res_up->execute();

                        $conexion->commit();

                        $message = "<div class='alert alert-success' style='background:linear-gradient(90deg,#e6ffed,#d4f7e2);border:1px solid #2ecc71;color:#155724;padding:12px 16px;border-radius:6px;font-weight:600;display:inline-block;'>Propietario y residencia actualizados correctamente.</div>";

                        // Limpiar y resetear estado
                        $rif = $nombre = $apellido = $usuario = $pass = $email_propietario = $numero_residencia = "";
                        $tipo_rif = "V";
                        $is_edit = false;
                    } catch (Exception $e) {
                        $conexion->rollback();
                        $message = "<div class='alert alert-danger'>Error al actualizar: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    $stmt_up->close();
                }
            }

            $stmt_check->close();
        }
    }

    // Eliminar miembro (desde tabla)
    if (!empty($_POST['action']) && $_POST['action'] === 'delete' && !empty($_POST['delete_rif'])) {
        $del_rif = $_POST['delete_rif'];
        $sql_del = "DELETE FROM propietario WHERE rif = ?";
        $stmt_del = $conexion->prepare($sql_del);
        $stmt_del->bind_param("s", $del_rif);
        if ($stmt_del->execute()) {
            $message = "<div class='alert alert-success' style='background:linear-gradient(90deg, #ffff, #ffffff);border:1px solid #ec0a0a;color:#ec0a0a;padding:12px 16px;border-radius:6px;box-shadow:0 2px 6px rgba(46,204,113,0.15);font-weight:600;display:inline-block;'>Propietario eliminado.</div>";
        } else {
            $message = "<div class='alert alert-danger'>No se pudo eliminar el propietario.</div>";
        }
        $stmt_del->close();
    }
} elseif (!empty($_GET['edit_rif'])) {
    // Cargar datos para edición vía GET
    $edit_rif = $_GET['edit_rif'];

    // --- CORRECCIÓN: Unificamos en una sola consulta con JOIN ---
    // Traemos los datos de propietario (p) y el nro de residencia (r) vinculando por ID
    $sql_get = "SELECT p.*, r.nro 
                FROM propietario p 
                LEFT JOIN residencias r ON p.id = r.id_propietario 
                WHERE p.rif = ?";

    $stmt_get = $conexion->prepare($sql_get);
    $stmt_get->bind_param("s", $edit_rif);
    $stmt_get->execute();
    $res_get = $stmt_get->get_result();

    if ($res_get && $res_get->num_rows > 0) {
        $row = $res_get->fetch_assoc();

        // Procesar RIF para el formulario
        $full_rif = $row['rif'];
        $tipo_rif = substr($full_rif, 0, 1);
        $rif = substr($full_rif, 1);

        // Asignar datos del propietario
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $usuario = $row['usuario'];
        $email_propietario = $row['email_propietario'];

        // --- CORRECCIÓN AQUÍ: Asignamos el nro obtenido del JOIN ---
        // Usamos el operador ?? '' por si el propietario no tiene residencia asignada aún
        $numero_residencia = $row['nro'] ?? '';

        // No rellenar la contraseña al editar por seguridad
        $pass = '';
        $is_edit = true;
    } else {
        $message = "<div class='alert alert-warning'>Registro no encontrado.</div>";
    }
    $stmt_get->close();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="./css/propietarios.css">
    <link rel="stylesheet" href="./css/carga_masiva.css">
    <link rel="stylesheet" href="./css/pagos.css"> <!-- agregado: estilos para la tabla de miembros -->
    <meta charset="UTF-8">
    <title>Añadir / Editar Miembro</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- barra de navegación -->
    <?php include 'navbar.php'; ?>
    <!-- PRINCIPAL -->
    <section class="principal">

        <?php
        if (!empty($message)) {
            echo $message;
        }
        ?>
        <div class="carga_pago">
            <form id="anadirMiembroForm" method="post" action="anadir_miembro.php<?= $is_edit ? '?edit_rif=' . urlencode(($tipo_rif . $rif)) : '' ?>">
                <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'add' ?>">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="original_rif" value="<?php echo htmlspecialchars($tipo_rif . $rif); ?>">
                <?php endif; ?>

                <label for="rif">RIF:</label>
                <div>
                    <select id="tipo_rif" name="tipo_rif" required>
                        <option value="V" <?= $tipo_rif === 'V' ? 'selected' : '' ?>>V</option>
                        <option value="J" <?= $tipo_rif === 'J' ? 'selected' : '' ?>>J</option>
                        <option value="G" <?= $tipo_rif === 'G' ? 'selected' : '' ?>>G</option>
                        <option value="E" <?= $tipo_rif === 'E' ? 'selected' : '' ?>>E</option>
                        <option value="C" <?= $tipo_rif === 'C' ? 'selected' : '' ?>>C</option>
                    </select>
                    <input type="text" id="rif" placeholder="Ejemplo: 12345678" name="rif" value="<?php echo htmlspecialchars($rif); ?>" required><br>
                </div>

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ejemplo: Juan" value="<?php echo htmlspecialchars($nombre); ?>" required><br>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" placeholder="Ejemplo: Pérez" value="<?php echo htmlspecialchars($apellido); ?>" required><br>

                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ejemplo: juanperez123" value="<?php echo htmlspecialchars($usuario); ?>" required><br>

                <label for="pass">Contraseña:</label>
                <input type="password" id="pass" name="pass" placeholder="Entre 8 y 12 caracteres" value="<?php echo htmlspecialchars($pass); ?>"><br>

                <label for="email_propietario">Email:</label>
                <input type="email" id="email_propietario" name="email_propietario" placeholder="Ejemplo: juan.perez@email.com" value="<?php echo htmlspecialchars($email_propietario); ?>" required><br>

                <label for="numero_residencia">Número de Residencia:</label>
                <input type="text" id="numero_residencia" name="numero_residencia" placeholder="Ejemplo: 101" value="<?php echo isset($numero_residencia) ? htmlspecialchars($numero_residencia) : ''; ?>" required><br>

                <button type="submit"><?php echo $is_edit ? 'Editar Miembro' : 'Añadir Miembro'; ?></button>

                <?php if ($is_edit): ?>
                    <a href="anadir_miembro.php" style="margin-left:10px;">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Lista de miembros activos -->

        <div class="carga_pago">
            <?php
            // Paginación
            $per_page = 2;
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($page - 1) * $per_page;

            // Total de registros
            $sql_count = "SELECT COUNT(*) AS total FROM propietario";
            $stmt_count = $conexion->prepare($sql_count);
            $total = 0;
            if ($stmt_count) {
                $stmt_count->execute();
                $res_count = $stmt_count->get_result();
                if ($res_count && $rowc = $res_count->fetch_assoc()) {
                    $total = intval($rowc['total']);
                }
                $stmt_count->close();
            }

            $total_pages = $total > 0 ? (int)ceil($total / $per_page) : 1;

            // Obtener página actual de registros
            // Cambiado: ordenar por id DESC para mostrar los más recientes primero
            $sql_list = "SELECT p.rif, p.nombre, p.apellido, p.usuario, p.email_propietario, r.nro AS residencia 
             FROM propietario p 
             LEFT JOIN residencias r ON p.id = r.id_propietario 
             ORDER BY p.id DESC LIMIT ?, ?";

            $stmt_list = $conexion->prepare($sql_list);
            if ($stmt_list) {
                $stmt_list->bind_param("ii", $offset, $per_page);
                $stmt_list->execute();
                $result_list = $stmt_list->get_result();

                if ($result_list && $result_list->num_rows > 0) {
                    echo '<div class="table-responsive"><table id="pagos-table" class="tabla-propietarios">
        <thead>
          <tr>
            <th>RIF</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Residencia</th> <th>Usuario</th>
            <th>Email</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>';

                    while ($row = $result_list->fetch_assoc()) {
                        $safe_rif = htmlspecialchars($row['rif']);
                        $safe_nombre = htmlspecialchars($row['nombre']);
                        $safe_apellido = htmlspecialchars($row['apellido']);
                        // Capturamos el nro de residencia (si es nulo ponemos N/A)
                        $safe_residencia = htmlspecialchars($row['residencia'] ?? 'N/A');
                        $safe_usuario = htmlspecialchars($row['usuario']);
                        $safe_email = htmlspecialchars($row['email_propietario']);

                        $edit_url = "anadir_miembro.php?edit_rif=" . urlencode($row['rif']) . "&page=" . $page;

                        echo "<tr>
                <td class='rif' data-label='RIF'>{$safe_rif}</td>
                <td class='nombre' data-label='Nombre'>{$safe_nombre}</td>
                <td class='apellido' data-label='Apellido'>{$safe_apellido}</td>
                <td class='residencia' data-label='Residencia'><strong>{$safe_residencia}</strong></td> 
                <td class='usuario' data-label='Usuario'>{$safe_usuario}</td>
                <td class='email' data-label='Email'>{$safe_email}</td>
                <td class='acciones-col' data-label='Acciones'>
                  <a href='{$edit_url}' class='btn-edit'>Editar</a>
                  <form method='post' style='display:inline-block;margin:0;padding:0;' onsubmit=\"return confirm('¿Eliminar propietario {$safe_nombre} {$safe_apellido}?');\">
                    <input type='hidden' name='action' value='delete'>
                    <input type='hidden' name='delete_rif' value='{$safe_rif}'>
                    <input type='hidden' name='page' value='{$page}'>
                    <button type='submit' class='btn-edit'>Eliminar</button>
                  </form>
                </td>
              </tr>";
                    }
                    echo "</tbody></table></div>";

                    // Paginador simple
                    echo '<div class="paginador" style="margin-top:12px;display:flex;gap:8px;align-items:center;">';
                    // Anterior
                    if ($page > 1) {
                        $prev = $page - 1;
                        echo "<a href=\"?page={$prev}\" class=\"btn-edit\">&laquo; Anterior</a>";
                    } else {
                        echo "<span style='opacity:.5;padding:6px 10px;border-radius:6px;background:#f5f5f5;'>&laquo; Anterior</span>";
                    }

                    // Números de página (mostrar máximo 5 páginas centradas)
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $start + 4);
                    if ($end - $start < 4) $start = max(1, $end - 4);

                    for ($p = $start; $p <= $end; $p++) {
                        if ($p == $page) {
                            echo "<span style='background:#4CAF50;color:#fff;padding:6px 10px;border-radius:6px;font-weight:700;'>{$p}</span>";
                        } else {
                            echo "<a href=\"?page={$p}\" class=\"btn-edit\">{$p}</a>";
                        }
                    }

                    // Siguiente
                    if ($page < $total_pages) {
                        $next = $page + 1;
                        echo "<a href=\"?page={$next}\" class=\"btn-edit\">Siguiente &raquo;</a>";
                    } else {
                        echo "<span style='opacity:.5;padding:6px 10px;border-radius:6px;background:#f5f5f5;'>Siguiente &raquo;</span>";
                    }

                    echo '</div>';
                } else {
                    echo "<p>No hay miembros registrados.</p>";
                }
                $stmt_list->close();
            } else {
                echo "<p>Error al obtener la lista de miembros.</p>";
            }

            $conexion->close();
            ?>
        </div>

    </section>
</body>

</html>