<?php require __DIR__ . '/../remember.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
<link href="/foroTodo/assets/css/nav.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-md navbar-dark bg-dark custom-navbar">
    <div class="container-fluid justify-content-center">
        <!-- Logo -->
        <a class="navbar-brand" href="/forotodo/php-login/index.php">ForoTodo</a>

        <!-- Botón colapsable en dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido de la navbar -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Sección izquierda de la navbar -->
            <ul class="navbar-nav w-100 justify-content-center">
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

                <!-- Elemento para administradores -->
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/forotodo/php-login/crud-usuarios.php">Usuarios</a>
                </li>
                <?php endif; ?>

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
            </ul>

            <!-- Sección derecha de la navbar -->
            <ul class="navbar-nav">
                <!-- Elementos de inicio de sesión y registro -->
                <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                    <a class="nav-link text-info" id="nav-user"
                        href="#"><?php echo $_SESSION['username']; ?></a>
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
