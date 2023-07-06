<?php 
  require 'remember.php';
?>

<link href="/foroTodo/assets/css/nav.css" rel="stylesheet" type="text/css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark custom-navbar">
    <div class="container-fluid d-flex justify-content-center align-items-center">
        <!-- Logo -->
        <a class="navbar-brand mx-auto" href="/forotodo/php-login/index.php">ForoTodo</a>


        <!-- Contenido de la navbar -->
        <div class="collapse navbar-collapse justify-content-center mx-auto" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Elementos de la navbar -->
                <li class="nav-item">
                    <a class="nav-link" href="/forotodo/php-login/index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Noticias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Navegar</a>
                </li>

                <!-- Elemento para agregar publicación -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/forotodo/php-publicaciones/agregar.php">Agregar publicación</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/forotodo/php-login/login.php?redirect=agregar.php">Agregar
                        publicación</a>
                </li>
                <?php endif; ?>

                <!-- Elemento para administradores -->
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/forotodo/php-login/crud-usuarios.php">Usuarios</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Sección derecha de la navbar -->
        <div class="ml-auto">
            <ul class="navbar-nav">
                <!-- Elementos de inicio de sesión y registro -->
                <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                    <a class="nav-link text-info" id="nav-user"
                        href="/forotodo/php-login/perfil.php"><?php echo $_SESSION['username']; ?></a>
                    <?php else: ?>
                    <a class="nav-link" id="nav-user"
                        href="/forotodo/php-login/perfil.php"><?php echo $_SESSION['username']; ?></a>
                    <?php endif; ?>
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