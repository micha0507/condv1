<?php
include './modelo/conexion.php';

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
    <title>Editor de publicaciones</title>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="principal">
        <h2>Editor de Nueva Publicación</h2>
        <?php if (isset($mensaje)): ?>
            <p><?php echo $mensaje; ?></p>
        <?php endif; ?>
        <form method="post">
            <div id="editor">
                <p>¡Escribe aquí tu contenido!</p>
            </div>
            <input type="hidden" name="contenido" id="contenido_input">
            <button class="boton_publicacion" type="submit">Guardar Publicación</button>
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
                if (contenidoHTML === '') {
                    mensajeDiv.textContent = 'El contenido no puede estar vacío.';
                    this.appendChild(mensajeDiv);
                    event.preventDefault(); // Evitar el envío del formulario si el contenido está vacío
                    return;
                }
                document.getElementById('contenido_input').value = contenidoHTML;
            } else {
                mensajeDiv.textContent = 'El editor aún no se ha inicializado.';
                this.appendChild(mensajeDiv);
                event.preventDefault(); // Evitar el envío del formulario si el editor no está listo
            }
        });
    </script>
</body>
</html>