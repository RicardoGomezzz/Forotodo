<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand mx-auto" href="#">ForoTodo</a>
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
        <li class="nav-item">
          <a class="nav-link" href="/forotodo/php-publicaciones/agregar.php">Agregar Publicacion</a>
        </li>
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