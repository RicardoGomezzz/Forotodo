<?php
require 'db.php';

session_start();

// Verificar si ya hay una sesión activa
if (isset($_SESSION['user_id'])) {
  header("Location: /forotodo/php-login/index.php");
  exit;
}

// Verificar si ya existe una cookie con una sesión activa
if (isset($_COOKIE['user_id'])) {
  $user_id = $_COOKIE['user_id'];

  // Buscar al usuario en la base de datos por su ID
  $query = "SELECT id FROM users WHERE id = :user_id";
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
        // Establece una cookie con el ID de usuario y su duración
        $cookie_duration = 30 * 24 * 60 * 60; // 30 días en segundos
        setcookie('user_id', $user['id'], time() + $cookie_duration);
      } else {
        // Elimina la cookie "user_id" si existe
        if (isset($_COOKIE['user_id'])) {
          setcookie('user_id', '', time() - 3600);
        }
      }
    
      $_SESSION['admin'] = ($user['admin'] == 1); // Almacena el valor booleano en la sesión
    
      if ($_SESSION['admin']) { // Verifica si el usuario es administrador
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
  <link href="/foroTodo/assets/css/login.css" rel="stylesheet" type="text/css">
</head>

<body>

  <?php include 'partials/header.php' ?>


  <br><br><br><br><br>

  <div class="container bg-white p-5 rounded-5 shadow mx-auto m-auto" style="width: 25rem;">
    <div class="d-flex justify-content-center">
      <img src="/forotodo/assets/img/perfil.png" alt="login-icon" style="height: 7rem;">
    </div>
    <div class="text-center fs-1 fw-bold">Inicia sesión</div>
    <div class="text-center">
      <?php if (!empty($message)) : ?>
        <div class="alert alert-danger mt-3" role="alert">
          <?= $message ?>
        </div>
      <?php endif; ?>
    </div>
    <form action="/forotodo/php-login/login.php" method="POST">
      <div class="input-group mt-4">
        <div class="input-group-text">
          <img src="/forotodo/assets/img/usuario.png" alt="user-icon" style="height: 1rem;">
        </div>
        <input class="form-control bg-light" type="text" name="email_or_username" placeholder="Correo o Usuario">
      </div>
      <div class="input-group mt-2">
        <div class="input-group-text ">
          <img src="/forotodo/assets/img/pass.png" alt="user-icon" style="height: 1rem;">
        </div>
        <input class="form-control bg-light" type="password" name="password" placeholder="Contraseña">
      </div>
      <br>
      <div class="d-flex justify-content-around mt-1">
        <div class="d-flex align-itsems-center gap-3">
          <input class="form-check-input" type="checkbox" name="recordar">
          <div class="pt-1" style="font-size: 0.9rem;">Recuérdame</div>
        </div>
      </div>
      <button type="submit" value="send" class="btn text-white w-100 mt-4 fw-semibold shadow-sm" style="background-color: cadetblue">Ingresar</button>
    </form>
    <br>
    <div class="d-flex gap-2 justify-content-center mt-1">
      <div style="font-size: 0.9rem;">¿No tienes cuenta?</div>
      <a href="/forotodo/php-login/registro.php" style="font-size: 0.9rem;" class="text-decoration-none text-info fw-semibold fst-italic">Regístrate</a>
    </div>
  </div>
</body>

</html>
