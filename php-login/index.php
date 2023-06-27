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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'partials/nav.php'; ?>


<div class="container">
  <h2>Publicaciones</h2>
  <?php foreach ($publicaciones as $publicacion) : ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?php echo $publicacion['titulo']; ?></h5>
        <p class="card-text"><?php echo $publicacion['contenido']; ?></p>
        <?php if ($publicacion['imagen']) : ?>
          <img src="<?php echo $publicacion['imagen']; ?>" class="card-img-top" alt="Imagen de la publicación">
        <?php endif; ?>
        <p class="card-text"><small class="text-muted"><?php echo $publicacion['fecha_publicacion']; ?></small></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>

</body>
</html>
