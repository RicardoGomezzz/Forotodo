<?php

session_start();

require 'remember.php';

require 'db.php';

$message = ''; // Variable para almacenar el mensaje de error

// Verificar si ya existe una sesión activa
if (isset($_SESSION['user_id'])) {
  header("Location: /forotodo/php-login/index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Validar que ambos campos estén completos
  if (empty($email) || empty($password)) {
    $message = 'Por favor, completa todos los campos.';
  } else {
    $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
    if ($stmt->execute()) {
      $message = '¡Cuenta creada exitosamente!';
    } else {
      $message = 'Hubo un error al crear la cuenta :(';
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
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link href="/foroTodo/assets/css/login.css" rel="stylesheet" type="text/css">
    <style>

    </style>
</head>

<body>
    <?php require 'partials/header.php' ?>

    

    <br><br><br>

    <div class="container bg-white p-5 rounded-5 shadow mx-auto m-auto" style="width: 30rem;">
        <div class="d-flex justify-content-center">
            <img src="/forotodo/assets/img/agregar-usuario.png" alt="login-icon" style="height: 7rem;">
        </div>
        <div class="text-center fs-1 fw-bold">Registra tu cuenta</div>
        <div class="text-center my-3">
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo ($messageClass === 'success') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $message; ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="/forotodo/php-login/registro.php" method="POST">
            <?php if (!empty($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>
            <div class="input-group mt-4">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/usuario.png" alt="user-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($userError)) echo 'is-invalid'; ?>" type="text" name="user" placeholder="Usuario" value="<?= $user ?? '' ?>">
                <?php if (!empty($userError)) : ?>
                    <div class="invalid-feedback">
                        <?= $userError ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/email.png" alt="email-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($emailError)) echo 'is-invalid'; ?>" type="email" name="email" placeholder="Correo electrónico" value="<?= $email ?? '' ?>">
                <?php if (!empty($emailError)) : ?>
                    <div class="invalid-feedback">
                        <?= $emailError ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($passwordError)) echo 'is-invalid'; ?>" type="password" name="password" placeholder="Contraseña">
                <?php if (!empty($passwordError)) : ?>
                    <div class="invalid-feedback">
                        <?= $passwordError ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text">
                    <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($confirmPasswordError)) echo 'is-invalid'; ?>" type="password" name="confirm_password" placeholder="Confirmar contraseña">
                <?php if (!empty($confirmPasswordError)) : ?>
                    <div class="invalid-feedback">
                        <?= $confirmPasswordError ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-primary px-5" type="submit">Registrarse</button>
            </div>
        </form>
        <br>
        <div class="d-flex gap-2 justify-content-center mt-1">
            <div style="font-size: 0.9rem;">¿Ya tienes cuenta?</div>
            <a href="/forotodo/php-login/login.php" style="font-size: 0.9rem;" class="text-decoration-none text-info fw-semibold fst-italic">Inicia Sesión</a>
        </div>
    </div>

</body>

</html>

