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
  $publications = $conn->query('SELECT * FROM publicaciones ORDER BY fecha_publicacion DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Foro - Barra de Navegación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/ForoTodo/assets/css/home.css" rel="stylesheet" type="text/css"> 
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand mx-auto" href="#">ForoTodo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Noticias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Navegar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="agregar.php">Agregar Publicacion</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Perfil</a>
        </li>
      </ul>
    </div>
    <div class="justify-content-end">
      <ul class="navbar-nav">
        <?php if ($username): ?>
          <li class="nav-item">
            <a class="nav-link" href="#"><?php echo $username; ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar sesión</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Iniciar sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="registro.php">Registro</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <br>
  <h2>Publicaciones</h2>
  <br>
  <?php foreach ($publications as $publication): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?php echo $publication['titulo']; ?></h5>
        <?php if (!empty($publication['imagen'])): ?>
        <img src="/ForoTodo/assets/<?php echo $publication['imagen']; ?>" alt="Imagen de la publicación">
        <?php endif; ?>
        <p class="card-text"><?php echo $publication['contenido']; ?></p>
        <p class="card-text">Fecha de publicación: <?php echo $publication['fecha_publicacion']; ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
