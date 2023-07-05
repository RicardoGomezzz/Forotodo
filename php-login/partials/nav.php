<link href="/foroTodo/assets/css/nav.css" rel="stylesheet" type="text/css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand mx-auto" href="/forotodo/php-login/index.php">ForoTodo</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/forotodo/php-login/index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Noticias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Navegar</a>
        </li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
        <a class="nav-link" href="/forotodo/php-publicaciones/agregar.php">Agregar publicación</a>
        </li>
      <?php else: ?>
        <li class="nav-item">
        <a class="nav-link" href="/forotodo/php-login/login.php?redirect=agregar.php">Agregar publicación</a>
        </li>
      <?php endif; ?>
      </ul>
    </div>
    <div class="justify-content-end">
      <ul class="navbar-nav">
        <?php if ($username): ?>
          <li class="nav-item">
            <a class="nav-link" href="/forotodo/php-login/perfil.php"><?php echo $username; ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar sesión</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/forotodo/php-login/login.php">Iniciar sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/forotodo/php-login/registro.php">Registro</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
