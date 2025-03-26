<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <title>Document</title>
</head>
<body>
      <!-- barra de navegación -->
    
      <?php include 'navbar.php'; ?>
     
    
<script src="https://cdn.ckeditor.com/ckeditor5/18.0.0/classic/ckeditor.js"></script>

<div class="principal">
<h1 class="titulo">Editor de publicaciones</h1>
<textarea type="text" name="txtDescripcion" id="txtDescripcion"></textarea> 
</div>

<script>
        ClassicEditor
            .create( document.querySelector( '#txtDescripcion' ) )
            .catch( error => {
            console.error( error );
            } );
        </script>
</body>
</html>
