<?php

  require 'db.php';

  $message = '';

  if (!empty($_POST['user']) && !empty($_POST['email']) && !empty($_POST['password'])) {
    $sql = "INSERT INTO users (user, email, password) VALUES (:user, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user', $_POST['user']);
    $stmt->bindParam(':email', $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
      $message = 'Usuario creado con exito';
    } else {
      $message = 'Ha ocurrido un error al crear su cuenta';
    }
  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="/ForoTodo/assets/css/login.css" rel="stylesheet" type="text/css">
</head>

<body>

    <?php require 'partials/header.php' ?>

    <?php if(!empty($message)): ?>
        <p> <?= $message ?></p>
    <?php endif; ?>



    <div class="container bg-white p-5 rounded-5 shadow mx-auto m-auto" style="width: 30rem;">
        <div class="d-flex justify-content-center">
            <img src="/forotodo/assets/img/agregar-usuario.png" alt="login-icon" style="height: 7rem;">
        </div>
        <div class="text-center fs-1 fw-bold">Registra tu cuenta</div>
        <form action="/forotodo/php-login/registro.php" method="POST">
            <div class="input-group mt-4">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/usuario.png" alt="user-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light" type="text" name="user" placeholder="Usuario">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/email.png" alt="email-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light" type="email" name="email" placeholder="Correo electrónico">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light" type="password" name="password" placeholder="Contraseña">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text">
                    <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light" type="password" name="confirm_password" placeholder="Confirmar contraseña">
            </div>
            <button type="submit" value="send" class="btn text-white w-100 mt-4 fw-semibold shadow-sm" style="background-color: cadetblue">Registrar</button>
        </form>
        <div class="d-flex gap-2 justify-content-center mt-1">
            <div style="font-size: 0.9rem;">¿Ya tienes una cuenta?</div>
            <a href="/forotodo/php-login/login.php" style="font-size: 0.9rem;" class="text-decoration-none text-info fw-semibold fst-italic">Iniciar sesión</a>
        </div>
        <div class="p-3">
            <div class="border-bottom text-center" style="height: 0.9rem;">
                <span style="font-size: 1rem;" class="bg-white px-3">Regístrate con</span>
            </div>
        </div>
        <div class="btn d-flex gap-2 justify-content-center border mt-3 shadow-sm">
            <img src="/forotodo/assets/img/google.png" alt="" style="height: 1.6rem;">
            <div class="fw-semibold text">Google</div>
        </div>
    </div>
</body>

</html>