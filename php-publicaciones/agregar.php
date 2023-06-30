<?php
session_start();

require 'remember.php';
require 'db.php';

$username = null;

if (isset($_SESSION['user_id'])) {
  $records = $conn->prepare('SELECT id, user, password FROM users WHERE id = :id');
  $records->bindParam(':id', $_SESSION['user_id']);
  $records->execute();
  $results = $records->fetch(PDO::FETCH_ASSOC);

  if (count($results) > 0) {
    $username = $results['user'];
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los valores ingresados por el usuario
  $titulo = $_POST['titulo'];
  $contenido = $_POST['contenido'];
  $imagen = $_FILES['imagen']['name'];
  $imagen_temporal = $_FILES['imagen']['tmp_name'];
  $carpeta_destino = 'img/';
  $imagen_ruta = '';

  if (!empty($imagen)) {
    $imagen_ruta = $carpeta_destino . basename($imagen);
    move_uploaded_file($imagen_temporal, $imagen_ruta);
  }

  // Insertar la publicación en la base de datos
  $stmt = $conn->prepare("INSERT INTO publicaciones (titulo, contenido, imagen) VALUES (:titulo, :contenido, :imagen)");
  $stmt->bindParam(':titulo', $titulo);
  $stmt->bindParam(':contenido', $contenido);
  $stmt->bindParam(':imagen', $imagen_ruta);
  $stmt->execute();

  // Redireccionar al index o a la página deseada después de agregar la publicación
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Foro - Barra de Navegación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <!-- Resto del código de la barra de navegación -->
</nav>

<div class="container">
  <h2>Agregar Publicación</h2>
  <form method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
      <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" class="form-control" id="titulo" name="titulo" required>
    </div>
    <div class="mb-3">
      <label for="contenido" class="form-label">Contenido</label>
      <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
    </div>
    <div class="mb-3">
    <label for="imagen" class="form-label">Imagen</label>
    <input type="file" class="form-control" id="imagen" name="imagen">
    </div>
    <button type="submit" class="btn btn-primary">Agregar</button>
  </form>
</div>

</body>
</html>