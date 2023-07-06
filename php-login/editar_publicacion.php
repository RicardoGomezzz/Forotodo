<?php
session_start();

require 'db.php';

// Verificar si se ha enviado una solicitud POST para editar la publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_publicacion'])) {
  if (isset($_SESSION['user_id'])) {
    $publicacionId = $_POST['publicacion_id'];
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];

    $stmt = $conn->prepare("UPDATE publicaciones SET titulo = :titulo, contenido = :contenido WHERE id = :publicacionId");
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':publicacionId', $publicacionId);
    $stmt->execute();
  } else {
    // El usuario no ha iniciado sesión
    // Redirigir o mostrar un mensaje de error
  }
}

// Obtener la ID de la publicación desde la URL o algún otro medio
$publicacionId = $_GET['id'];

// Obtener los datos de la publicación desde la base de datos usando la ID
$stmt = $conn->prepare("SELECT * FROM publicaciones WHERE id = :publicacionId");
$stmt->bindParam(':publicacionId', $publicacionId);
$stmt->execute();
$publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró la publicación
if (!$publicacion) {
  // La publicación no existe, redireccionar o mostrar un mensaje de error
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Publicación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php
// ...

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_publicacion'])) {
  // Mostrar un mensaje de éxito después de editar la publicación
  echo '<div class="alert alert-success" role="alert">La publicación ha sido editada exitosamente.</div>';
}

?>

<div class="container">
  <h2>Editar Publicación</h2>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($publicacion['titulo']); ?>" required>
    </div>
    <div class="mb-3">
      <label for="contenido" class="form-label">Contenido</label>
      <textarea class="form-control" id="contenido" name="contenido" rows="5" required><?php echo htmlspecialchars($publicacion['contenido']); ?></textarea>
    </div>
    <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id']; ?>">
    <button type="submit" name="editar_publicacion" class="btn btn-primary">Guardar Cambios</button>
  </form>
</div>

</body>
</html>
