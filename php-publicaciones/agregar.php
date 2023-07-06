<?php
session_start();

require '../php-login/db.php';
require '../php-login/remember.php';

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
  $tema = $_POST['tema'];
  $imagen_temporal = $_FILES['imagen']['tmp_name'];
  $carpeta_destino = '../assets/img/';
  $imagen_ruta = '';

  $maxSize = 2 * 1024 * 1024; // Tamaño máximo permitido en bytes (2 MB en este caso)

  if (!empty($imagen_temporal)) {
    if ($_FILES['imagen']['size'] > $maxSize) {
      echo '<script>alert("La imagen es demasiado grande. Por favor, elige una imagen más pequeña.");</script>';
      exit();
    }

    $imagen_nombre = $_FILES['imagen']['name'];
    $imagen_ruta = $carpeta_destino . $imagen_nombre;

    // Redimensionar la imagen
    $imagen = imagecreatefromstring(file_get_contents($imagen_temporal));
    $ancho_original = imagesx($imagen);
    $alto_original = imagesy($imagen);

    $maxWidth = 800; // Ancho máximo permitido
    $maxHeight = 600; // Alto máximo permitido

    $nuevo_ancho = $ancho_original;
    $nuevo_alto = $alto_original;

    // Redimensionar solo si la imagen excede los límites establecidos
    if ($ancho_original > $maxWidth || $alto_original > $maxHeight) {
      $ratio = $ancho_original / $alto_original;

      if ($maxWidth / $maxHeight > $ratio) {
        $nuevo_ancho = $maxHeight * $ratio;
        $nuevo_alto = $maxHeight;
      } else {
        $nuevo_ancho = $maxWidth;
        $nuevo_alto = $maxWidth / $ratio;
      }

      $imagen_redimensionada = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
      imagecopyresampled($imagen_redimensionada, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho_original, $alto_original);

      // Guardar la imagen redimensionada
      imagejpeg($imagen_redimensionada, $imagen_ruta, 80);

      imagedestroy($imagen_redimensionada); // Destruir la imagen redimensionada
    } else {
      move_uploaded_file($imagen_temporal, $imagen_ruta);
    }

    imagedestroy($imagen); // Destruir la imagen original
  }
  
  // Insertar la publicación en la base de datos
  
  $stmt = $conn->prepare("INSERT INTO publicaciones (titulo, contenido, imagen, tema, user_id) VALUES (:titulo, :contenido, :imagen, :tema, :user_id)");
  $stmt->bindParam(':titulo', $titulo);
  $stmt->bindParam(':contenido', $contenido);
  $stmt->bindParam(':imagen', $imagen_ruta);
  $stmt->bindParam(':tema', $tema);
  $stmt->bindParam(':user_id', $_SESSION['user_id']);
  $stmt->execute();

  // Redireccionar al index o a la página deseada después de agregar la publicación
  header("Location: /forotodo/php-login/index.php");
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

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ForoTodo/php-login/partials/nav.php'; ?>

    <div class="container">
        <h2>Agregar Publicación</h2>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($imagen_temporal) && $_FILES['imagen']['size'] > $maxSize): ?>
        <div class="alert alert-danger" role="alert">
            La imagen es demasiado grande. Por favor, elige una imagen más pequeña.
        </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data"
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="mb-3">
                <label for="contenido" class="form-label">Contenido</label>
                <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label for="tema" class="form-label">Tema</label>
                <select class="form-control" id="tema" name="tema">
                    <option value="Tecnologia">Tecnología</option>
                    <option value="Deportes">Deportes</option>
                    <option value="Cine">Cine</option>
                    <option value="Música">Música</option>
                </select>
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