<?php
require 'db.php';

session_start();

require 'remember.php';

// Verificar si ya hay una sesión activa
if (isset($_SESSION['user_id'])) {
  header("Location: /forotodo/php-login/index.php");
  exit;
}

// Verificar si ya existe una cookie con una sesión activa
if (isset($_COOKIE['user_id'])) {
  $user_id = $_COOKIE['user_id'];

  // Buscar al usuario en la base de datos por su ID
  $query = "SELECT id, user FROM users WHERE id = :user_id";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['user']; // Almacenar el nombre de usuario en la sesión
    header("Location: /forotodo/php-login/index.php");
    exit;
  } else {
    // Eliminar la cookie "user_id" si el usuario no existe en la base de datos
    setcookie('user_id', '', time() - 3600);
  }
}

$message = ''; // Variable para almacenar el mensaje de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $emailOrUsername = $_POST['email_or_username'];
  $password = $_POST['password'];

  // Validar que ambos campos estén completos
  if (empty($emailOrUsername) || empty($password)) {
    $message = 'Por favor, completa todos los campos';
  } else {
    // Verificar si el usuario está intentando iniciar sesión como administrador
    $query = "SELECT id, user, email, password, admin FROM users WHERE email = :email OR user = :user";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $emailOrUsername);
    $stmt->bindParam(':user', $emailOrUsername);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['user']; // Almacenar el nombre de usuario en la sesión

      // Si se seleccionó "Recordar sesión"
      if (!empty($_POST['recordar'])) {
        // Establecer una cookie con el ID de usuario y su duración
        $cookie_duration = 30 * 24 * 60 * 60; // 30 días en segundos
        setcookie('user_id', $user['id'], time() + $cookie_duration);
      } else {
        // Eliminar la cookie "user_id" si existe
        if (isset($_COOKIE['user_id'])) {
          setcookie('user_id', '', time() - 3600);
        }
      }

      $_SESSION['admin'] = ($user['admin'] == 1); // Almacenar el valor booleano en la sesión

      if ($_SESSION['admin']) { // Verificar si el usuario es administrador
        header("Location: /forotodo/php-login/index.php?admin=true");
      } else {
        header("Location: /forotodo/php-login/index.php");
      }
      exit;
    } else {
      $message = 'Las credenciales no coinciden ;(';
    }      
  }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio De Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link href="/foroTodo/assets/css/login.css" rel="stylesheet" type="text/css">
</head>

<body>

    <?php include 'partials/header.php' ?>

    <div class="container bg-white p-5 rounded-5 shadow-sm mt-5 mx-auto justify-content-center align-items-center"
        style="max-width: 400px;">
        <div class="text-center">
            <img src="/forotodo/assets/img/perfil.png" alt="login-icon" class="mb-4" style="height: 7rem;">
            <h1 class="fw-bold">Inicia sesión</h1>
        </div>

        <?php if (!empty($message)) : ?>
        <div class="alert alert-danger mt-3" role="alert">
            <?= $message ?>
        </div>
        <?php endif; ?>

        <form action="/forotodo/php-login/login.php" method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <img src="/forotodo/assets/img/usuario.png" alt="user-icon" style="height: 1rem;">
                    </span>
                    <input class="form-control" type="text" name="email_or_username" placeholder="Correo o Usuario">
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <img src="/forotodo/assets/img/pass.png" alt="password-icon" style="height: 1rem;">
                    </span>
                    <input class="form-control" type="password" name="password" placeholder="Contraseña">
                </div>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="recordar" id="rememberCheck">
                <label class="form-check-label" for="rememberCheck">Recuérdame</label>
            </div>

            <button type="submit" class="btn text-white w-100 mt-4 fw-semibold shadow-sm btn-block"
                style="background-color: cadetblue;">Ingresar</button>
        </form>

        <div class="text-center mt-4">
            ¿No tienes cuenta? <a href="/forotodo/php-login/registro.php"
                class="text-decoration-none text-info fw-semibold fst-italic">Regístrate</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>