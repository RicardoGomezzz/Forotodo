<?php
session_start();


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

// Obtener las publicaciones ordenadas por fecha de publicación
$stmt = $conn->query('SELECT * FROM publicaciones ORDER BY fecha_publicacion DESC');
$publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ForoTodo - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/forotodo/assets/css/home.css" rel="stylesheet" type="text/css"> 
  <link rel="preconnect" href="https://fonts.googleapis.com%22%3E/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/nav.php'; ?>


<div class="container">
  <br>
  <h2>Publicaciones</h2>
  <br>
  <?php foreach ($publicaciones as $publication): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?php echo $publication['titulo']; ?></h5>
        <?php if (!empty($publication['imagen']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/ForoTodo/assets/'.$publication['imagen'])): ?>
      <img src="/ForoTodo/assets/<?php echo $publication['imagen']; ?>" alt="Imagen de la publicación">
      <?php endif; ?>
      <?php if (empty($publication['imagen'])): ?>
        <!-- Aquí no se muestra nada cuando no hay imagen -->
      <?php endif; ?>
        <p class="card-text"><?php echo $publication['contenido']; ?></p>
        <p class="card-text">Fecha de publicación: <?php echo $publication['fecha_publicacion']; ?></p>

        <?php
        // Recuperar los comentarios de la publicación actual
        $comentariosStmt = $conn->prepare("SELECT * FROM comentarios WHERE publicacion_id = :publicacionId");
        $comentariosStmt->bindParam(':publicacionId', $publication['id']);
        $comentariosStmt->execute();
        $comentarios = $comentariosStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

      <!-- Mostrar los comentarios -->
      <div class="comentarios">
      <?php foreach ($comentarios as $comentario): ?>
      <div class="comentario">
      <strong><?php echo $comentario['usuario']; ?>:</strong>
      <?php echo $comentario['contenido']; ?>
    </div>
  <?php endforeach; ?>
</div>
      </div>
      <form method="POST" action="procesar_comentario.php">
        <input type="hidden" name="publicacion_id" value="<?php echo $publication['id']; ?>">
        <input type="hidden" name="usuario" value="<?php echo $username; ?>">
        <div class="mb-3">
        <label for="comentario">Comentario:</label>
        <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar comentario</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>

</body>
</html>