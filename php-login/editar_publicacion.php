<?php
session_start();

require 'db.php';

// Verificar si se ha enviado una solicitud POST para editar la publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_publicacion'])) {
  if (isset($_SESSION['user_id'])) {
    $publicacionId = $_POST['publicacion_id'];
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $borrarImagen = isset($_POST['borrar_imagen']) && $_POST['borrar_imagen'] === 'on';
    if ($borrarImagen) {
      // Eliminar la referencia a la imagen en la base de datos
      $stmt = $conn->prepare("UPDATE publicaciones SET titulo = :titulo, contenido = :contenido, imagen = NULL WHERE id = :publicacionId");
      $stmtImagen = $conn->prepare("SELECT imagen FROM publicaciones WHERE id = :publicacionId");
      $stmtImagen->bindParam(':publicacionId', $publicacionId);
      $stmtImagen->execute();
      $imagenAnterior = $stmtImagen->fetch(PDO::FETCH_ASSOC)['imagen'];
      if (!empty($imagenAnterior)) {
        $rutaImagenAnterior = $_SERVER['DOCUMENT_ROOT'] . '/ForoTodo/assets/' . $imagenAnterior;
        if (file_exists($rutaImagenAnterior)) {
          unlink($rutaImagenAnterior);
        }
      }
    } else {
      // Actualizar la publicación sin modificar la imagen
      $stmt = $conn->prepare("UPDATE publicaciones SET titulo = :titulo, contenido = :contenido WHERE id = :publicacionId");
    }
    if ($borrarImagen) {
      // Eliminar el archivo de imagen relacionado con la publicación
      $imagenRuta = $_SERVER['DOCUMENT_ROOT'] . 'C:\xampp\htdocs\ForoTodo\assets\img';
      if (file_exists($imagenRuta)) {
        unlink($imagenRuta);
      }
    }

    // Verificar si se ha subido una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
      $imagen = $_FILES['imagen'];
      $imagenNombre = $imagen['name'];
      $imagenTmpPath = $imagen['tmp_name'];
      $imagenType = $imagen['type'];

      // Obtener la extensión del archivo
      $imagenExtension = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));

      // Validar la extensión de la imagen
      $extensionesValidas = array('jpg', 'jpeg', 'png');
      if (!in_array($imagenExtension, $extensionesValidas)) {
        // Mostrar un mensaje de error si la extensión no es válida
        $errorImagen = 'La extensión del archivo no es válida. Por favor, sube una imagen JPG, JPEG o PNG.';
      } else {
        // Mover la imagen a la ubicación deseada
        $imagenDestino = '../assets/img/' . uniqid('', true) . '.' . $imagenExtension;
        move_uploaded_file($imagenTmpPath, $imagenDestino);

        // Actualizar la base de datos con la nueva imagen
        $stmt = $conn->prepare("UPDATE publicaciones SET titulo = :titulo, contenido = :contenido, imagen = :imagen WHERE id = :publicacionId");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':imagen', $imagenDestino);
        $stmt->bindParam(':publicacionId', $publicacionId);
        $stmt->execute();
      }
    } else {
      // No se ha subido una nueva imagen, solo se actualizan el título y el contenido
      $stmt = $conn->prepare("UPDATE publicaciones SET titulo = :titulo, contenido = :contenido WHERE id = :publicacionId");
      $stmt->bindParam(':titulo', $titulo);
      $stmt->bindParam(':contenido', $contenido);
      $stmt->bindParam(':publicacionId', $publicacionId);
      $stmt->execute();
    }

    // Redirigir al índice después de guardar los cambios
    header("Location: index.php");
    exit();
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
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($publicacion['titulo']); ?>" required>
    </div>
    <div class="mb-3">
      <label for="contenido" class="form-label">Contenido</label>
      <textarea class="form-control" id="contenido" name="contenido" rows="5" required><?php echo htmlspecialchars($publicacion['contenido']); ?></textarea>
    </div>
    <div class="mb-3">
      <label for="imagen" class="form-label">Imagen</label>
      <input type="file" class="form-control" id="imagen" name="imagen">
      <?php if (isset($errorImagen)): ?>
        <div class="text-danger"><?php echo $errorImagen; ?></div>
      <?php endif; ?>
    </div>
    <div class="mb-3">
      <label for="borrar_imagen" class="form-label">Borrar imagen</label>
      <input type="checkbox" id="borrar_imagen" name="borrar_imagen" value="on">
    </div>
    <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id']; ?>">
    <button type="submit" name="editar_publicacion" class="btn btn-primary">Guardar Cambios</button>
  </form>
</div>

</body>
</html>
