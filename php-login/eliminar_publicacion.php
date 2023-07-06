<?php
session_start();

require 'db.php';

// Verificar si se ha enviado una solicitud POST para eliminar la publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_publicacion'])) {
  if (isset($_SESSION['user_id'])) {
    $publicacionId = $_POST['publicacion_id'];

    $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id = :publicacionId");
    $stmt->bindParam(':publicacionId', $publicacionId);
    $stmt->execute();

    // Redirigir a la página de publicaciones o mostrar un mensaje de éxito
    header('Location: index.php');
    exit();
  } else {
    // El usuario no ha iniciado sesión
    // Redirigir o mostrar un mensaje de error
    header('Location: iniciar_sesion.php');
    exit();
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
  header('Location: index.php');
  exit();
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eliminar Publicación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
  <h2>Eliminar Publicación</h2>
  <p>¿Estás seguro de que deseas eliminar esta publicación?</p>
  <h3>Título: <?php echo htmlspecialchars($publicacion['titulo']); ?></h3>
  <p>Contenido: <?php echo htmlspecialchars($publicacion['contenido']); ?></p>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id']; ?>">
    <button type="submit" name="eliminar_publicacion" class="btn btn-danger">Eliminar</button>
    <a href="publicaciones.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

</body>
</html>
