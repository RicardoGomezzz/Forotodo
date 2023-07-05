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
$stmt = $conn->query('SELECT id, titulo, contenido, imagen, fecha_publicacion, tema FROM publicaciones ORDER BY fecha_publicacion DESC');
$publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtrar las publicaciones por tema si se ha enviado el parámetro
if (isset($_GET['tema']) && $_GET['tema'] !== '') {
  $temaFiltrado = $_GET['tema'];
  $publicaciones = array_filter($publicaciones, function($publication) use ($temaFiltrado) {
    return $publication['tema'] === $temaFiltrado;
  });
}
// Obtener la fecha actual
$fechaActual = date('Y-m-d');

// Filtrar las publicaciones por tema y fecha si se han enviado los parámetros
if (isset($_GET['tema']) && $_GET['tema'] !== '') {
  $temaFiltrado = $_GET['tema'];
  $publicaciones = array_filter($publicaciones, function($publication) use ($temaFiltrado) {
    return $publication['tema'] === $temaFiltrado;
  });
}

if (isset($_GET['intervalo'])) {
  $intervalo = $_GET['intervalo'];

  switch ($intervalo) {
    case '24h':
      // Filtrar por las últimas 24 horas
      $fechaInicio = date('Y-m-d H:i:s', strtotime('-24 hours'));
      $publicaciones = array_filter($publicaciones, function($publication) use ($fechaInicio, $fechaActual) {
        $fechaPublicacion = date('Y-m-d', strtotime($publication['fecha_publicacion']));
        return ($fechaPublicacion >= $fechaInicio && $fechaPublicacion <= $fechaActual);
      });
      break;
    case '1w':
      // Filtrar por la última semana
      $fechaInicio = date('Y-m-d H:i:s', strtotime('-1 week'));
      $publicaciones = array_filter($publicaciones, function($publication) use ($fechaInicio, $fechaActual) {
        $fechaPublicacion = date('Y-m-d', strtotime($publication['fecha_publicacion']));
        return ($fechaPublicacion >= $fechaInicio && $fechaPublicacion <= $fechaActual);
      });
      break;
    case '1m':
      // Filtrar por el último mes
      $fechaInicio = date('Y-m-d H:i:s', strtotime('-1 month'));
      $publicaciones = array_filter($publicaciones, function($publication) use ($fechaInicio, $fechaActual) {
        $fechaPublicacion = date('Y-m-d', strtotime($publication['fecha_publicacion']));
        return ($fechaPublicacion >= $fechaInicio && $fechaPublicacion <= $fechaActual);
      });
      break;
  }
}
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
  <form method="GET" action="">
    <div class="mb-3">
      <label for="tema">Filtrar por tema:</label>
      <select name="tema" id="tema">
        <option value="">Todos los temas</option>
        <option value="Tecnologia">Tecnología</option>
        <option value="Deportes">Deportes</option>
        <option value="Cine">Cine</option>
        <option value="Música">Música</option>
      </select>
    <div class="mb-3">
      <label for="intervalo">Filtrar por intervalo de tiempo:</label>
    <div>
      <input type="radio" id="24h" name="intervalo" value="24h">
      <label for="24h">Últimas 24 horas</label>
    </div>
    <div>
      <input type="radio" id="1w" name="intervalo" value="1w">
      <label for="1w">Última semana</label>
    </div>
    <div>
      <input type="radio" id="1m" name="intervalo" value="1m">
      <label for="1m">Último mes</label>
    </div>
    </div>
      <button type="submit" class="btn btn-primary">Filtrar</button>
    </div>
  </form>
  <?php foreach ($publicaciones as $publication): ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?php echo $publication['titulo']; ?></h5>
        <p class="card-text">Tema: <?php echo $publication['tema']; ?></p>
        <?php if (!empty($publication['imagen']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/ForoTodo/assets/'.$publication['imagen'])): ?>
        <div class="image-container">
          <img src="/ForoTodo/assets/<?php echo $publication['imagen']; ?>" alt="Imagen de la publicación" class="img-responsive">
        </div>      
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
      
      <?php if (isset($_SESSION['user_id'])): ?>
      <form method="POST" action="../php-publicaciones/procesar_comentario.php">
        <input type="hidden" name="publicacion_id" value="<?php echo $publication['id']; ?>">
        <input type="hidden" name="usuario" value="<?php echo $username; ?>">
        <div class="mb-3">
        <label for="comentario">Comentario:</label>
        <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar comentario</button>
      </form>
      <?php else: ?>
        <p>Por favor, <a href="/forotodo/php-login/login.php?redirect=index.php">inicia sesión</a> para agregar un comentario.</p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>

</body>
</html>