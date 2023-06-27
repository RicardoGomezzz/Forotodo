<?php

require 'db.php';
require 'remember.php';

session_start();



$userError = $emailError = $passwordError = $confirmPasswordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validar que todos los campos estén completos
    if (empty($user) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'Por favor, completa todos los campos del formulario.';
    } else {
        // Validar la longitud de la contraseña
        if (strlen($password) < 8) {
            $passwordError = 'La contraseña debe tener al menos 8 caracteres.';
        }

        // Verificar que las contraseñas sean iguales
        if ($password !== $confirmPassword) {
            $confirmPasswordError = 'Las contraseñas no coinciden.';
        }

        // Si no hay errores, continuar con el proceso de registro
        if (empty($passwordError) && empty($confirmPasswordError)) {
            // Verificar si el usuario o la dirección de correo electrónico ya existen en la base de datos
            $checkQuery = "SELECT * FROM users WHERE user = :user OR email = :email LIMIT 1";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':user', $user);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                if ($existingUser['user'] === $user) {
                    $userError = 'El nombre de usuario ya está en uso.';
                } elseif ($existingUser['email'] === $email) {
                    $emailError = 'La dirección de correo electrónico ya está en uso.';
                }
            } else {
                // Si pasa todas las validaciones, insertar los datos en la base de datos
                $sql = "INSERT INTO users (user, email, password) VALUES (:user, :email, :password)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user', $user);
                $stmt->bindParam(':email', $email);
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt->bindParam(':password', $hashedPassword);

                if ($stmt->execute()) {
                    $message = 'Usuario creado con éxito, ya puedes iniciar sesión';
                    $messageClass = 'success';
                } else {
                    $message = 'Ha ocurrido un error al crear su cuenta';
                    $messageClass = 'error';
                }
                
            }
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
                <div class="alert <?= ($messageClass === 'success') ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?= $message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($userError)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $userError ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($emailError)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $emailError ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($passwordError)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $passwordError ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($confirmPasswordError)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $confirmPasswordError ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="/forotodo/php-login/registro.php" method="POST">
            <div class="input-group mt-4">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/usuario.png" alt="user-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($userError)) echo ''; ?>" type="text" name="user" placeholder="Usuario" value="<?= $user ?? '' ?>">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/email.png" alt="email-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($emailError)) echo ''; ?>" type="email" name="email" placeholder="Correo electrónico" value="<?= $email ?? '' ?>">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text ">
                    <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($passwordError)) echo ''; ?>" type="password" name="password" placeholder="Contraseña">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text">
                    <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                </div>
                <input class="form-control bg-light <?php if (!empty($confirmPasswordError)) echo ''; ?>" type="password" name="confirm_password" placeholder="Confirmar contraseña">
            </div>
            <div class="text-center mt-4">
                <button type="submit" value="send" class="btn text-white w-100 mt-4 fw-semibold shadow-sm" style="background-color: cadetblue">Registrarse</button>
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


