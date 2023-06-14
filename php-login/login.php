<?php

  session_start();

  if (isset($_SESSION['user_id'])) {
    header('Location: /forotodo/php-login/login.php');
  }
  require 'db.php';

  if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $records = $conn->prepare('SELECT id, email, password FROM users WHERE email = :email');
    $records->bindParam(':email', $_POST['email']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $message = '';

    if (count($results) > 0 && password_verify($_POST['password'], $results['password'])) {
      $_SESSION['user_id'] = $results['id'];
      header("Location: /forotodo/php-login/index.php");
    } else {
      $message = 'Las credenciales no coinciden ;(';
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
    <link href="/ForoTodo/assets/css/login.css" rel="stylesheet" type="text/css">
</head>

<body>
    
    <?php require 'partials/header.php' ?>

    <?php if(!empty($message)): ?>
        <p> <?= $message ?></p>
    <?php endif; ?>
    
    <div class="container bg-white p-5 rounded-5 shadow mx-auto m-auto" style="width: 25rem;">
        <div class="d-flex justify-content-center">
            <img src="/forotodo/assets/img/perfil.png" alt="login-icon" style="height: 7rem;">
        </div>
        <div class="text-center fs-1 fw-bold">Inicia sesión</div>
        <form action="/forotodo/php-login/login.php" method="POST">
            <div class="input-group mt-4">
                <div class="input-group-text">
                    <img src="/forotodo/assets/img/usuario.png" alt="user-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light" type="text" name="email" placeholder="Correo">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/pass.png" alt="user-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light" type="password" name="password" placeholder="Contraseña">
            </div>
            <div class="d-flex justify-content-around mt-1">
                <div class="d-flex align-items-center gap-3">
                    <input class="form-check-input" type="checkbox" name="recordar"> 
                    <div class="pt-1" style="font-size: 0.9rem;">Recuérdame</div>
                    <a href="#" class="pt-1 text-decoration-none text-info fw-semibold fst-italic" style="font-size: 0.9rem;">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
            <button type="submit" value="send" class="btn text-white w-100 mt-4 fw-semibold shadow-sm" style="background-color: cadetblue">Ingresar</button>
        </form>
        <div class="d-flex gap-2 justify-content-center mt-1">
            <div style="font-size: 0.9rem;">¿No tienes cuenta?</div>
            <a href="/forotodo/php-login/registro.php" style="font-size: 0.9rem;" class="text-decoration-none text-info fw-semibold fst-italic">Regístrate</a>
        </div>
        <div class="p-3">
            <div class="border-bottom text-center" style="height: 0.9rem;">
                <span style="font-size: 1rem;" class="bg-white px-3 ">Ingresar con</span>
            </div>
        </div>
        <div class="btn d-flex gap-2 justify-content-center border mt-3 shadow-sm">
            <img src="/forotodo/assets/img/google.png" alt="" style="height: 1.6rem;">
            <div class="fw-semibold">Google</div>
        </div>
    </div>
</body>

</html>