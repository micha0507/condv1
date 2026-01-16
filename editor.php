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

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el contenido del editor
    $contenido = $_POST['contenido'];

    try {
        // Preparar la consulta SQL para insertar el contenido
        $pdo = new PDO("mysql:host=localhost;dbname=condominio", "root", "");
        $stmt = $pdo->prepare("INSERT INTO post (contenido) VALUES (:contenido)");

        // Bindear el parámetro
        $stmt->bindParam(':contenido', $contenido);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $mensaje = "Publicación guardada exitosamente.";
        } else {
            $mensaje = "Error al guardar la publicación.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error de base de datos: " . $e->getMessage();
    } finally {
        // Cerrar la conexión a la base de datos
        $pdo = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/publicacion.css">
    <title>Editor de publicaciones</title>
       <link rel="icon" href="/img/ico_condo.ico">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="principal">
        <h2>Crear Nueva Publicación</h2>
        <?php if (isset($mensaje)): ?>
            <p><?php echo $mensaje; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="pub-card">
                <div class="pub-row">
                    <label class="label">Título</label>
                    <input type="text" id="titulo" class="input" placeholder="Título de la publicación (opcional)">
                </div>
                <div class="pub-row">
                    <label class="label">Destinado a</label>
                    <select id="audiencia" class="input">
                        <option value="Administración">Personal administrativo</option>
                        <option value="Propietarios">Propietarios</option>
                        <option value="General">Todos</option>
                    </select>
                </div>
                <div id="editor" class="editor">
                    <p>¡Escribe aquí tu contenido!</p>
                </div>
                <input type="hidden" name="contenido" id="contenido_input">
                <div class="pub-actions">
                    <button class="btn btn-secondary" type="button" id="btn_preview">Vista previa</button>
                    <button class="btn btn-primary" type="submit">Guardar Publicación</button>
                </div>
                <div id="preview_area" class="preview_area" style="display:none;"></div>
            </div>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        let editor;

        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( newEditor => {
                editor = newEditor;
            } )
            .catch( error => {
                console.error( error );
            } );

        // Función para actualizar el valor del input oculto antes de enviar el formulario
        document.querySelector('form').addEventListener('submit', function(event) {
            const mensajeDiv = document.createElement('div');
            mensajeDiv.id = 'mensaje_error';
            mensajeDiv.style.color = 'red';
            mensajeDiv.style.marginTop = '10px';

            // Eliminar mensaje previo si existe
            const mensajePrevio = document.getElementById('mensaje_error');
            if (mensajePrevio) {
                mensajePrevio.remove();
            }

            if (editor) {
                const contenidoHTML = editor.getData().trim();
                const titulo = document.getElementById('titulo').value.trim();
                const audiencia = document.getElementById('audiencia').value;
                if (contenidoHTML === '' && titulo === '') {
                    mensajeDiv.textContent = 'El contenido no puede estar vacío.';
                    this.appendChild(mensajeDiv);
                    event.preventDefault();
                    return;
                }

                // Preparamos el contenido final incluyendo título y audiencia
                let finalHTML = '';
                if (titulo !== '') {
                    finalHTML += '<h2>' + titulo.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</h2>';
                }
                finalHTML += '<div class="audiencia_meta">Para: ' + audiencia + '</div>';
                finalHTML += contenidoHTML;

                document.getElementById('contenido_input').value = finalHTML;
            } else {
                mensajeDiv.textContent = 'El editor aún no se ha inicializado.';
                this.appendChild(mensajeDiv);
                event.preventDefault();
            }
        });

        // Vista previa
        document.getElementById('btn_preview').addEventListener('click', function() {
            if (!editor) return;
            const contenidoHTML = editor.getData().trim();
            const titulo = document.getElementById('titulo').value.trim();
            const audiencia = document.getElementById('audiencia').value;
            let finalHTML = '';
            if (titulo !== '') finalHTML += '<h2>' + titulo.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</h2>';
            finalHTML += '<div class="audiencia_meta">Para: ' + audiencia + '</div>';
            finalHTML += contenidoHTML || '<p><em>Sin contenido aún</em></p>';
            const preview = document.getElementById('preview_area');
            preview.innerHTML = finalHTML;
            preview.style.display = preview.style.display === 'none' ? 'block' : 'none';
            preview.scrollIntoView({behavior: 'smooth'});
        });
    </script>
</body>
</html>