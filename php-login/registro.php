<?php
require 'db.php';

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
            $message = 'La contraseña debe tener al menos 8 caracteres.';
        }

        // Verificar que las contraseñas sean iguales
        if ($password !== $confirmPassword) {
            $message = 'Las contraseñas no coinciden.';
        }

        // Si no hay errores, continuar con el proceso de registro
        if (empty($message) && empty($message)) {
            // Verificar si el usuario o la dirección de correo electrónico ya existen en la base de datos
            $checkQuery = "SELECT * FROM users WHERE user = :user OR email = :email LIMIT 1";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':user', $user);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                if ($existingUser['user'] === $user) {
                    $message = 'El nombre de usuario ya está en uso.';
                } elseif ($existingUser['email'] === $email) {
                    $message = 'La dirección de correo electrónico ya está en uso.';
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
                    $message = '<div class="message success">Usuario creado con éxito, ya puedes iniciar sesión</div>';
                } else {
                    $message = '<div class="message error">Ha ocurrido un error al crear su cuenta</div>';
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
    <style>

    </style>
</head>

<body>
    <?php require 'partials/header.php' ?>

    

    <br>

    <div class="container bg-white p-5 rounded-5 shadow mx-auto m-auto" style="width: 30rem;">
        <div class="d-flex justify-content-center">
            <img src="/forotodo/assets/img/agregar-usuario.png" alt="login-icon" style="height: 7rem;">
        </div>
        <div class="text-center fs-1 fw-bold">Registra tu cuenta</div>
        <div class="text-center">
            <?php if(!empty($message)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?= $message ?>
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
        <div class="text-center mt-4">
            ¿Ya tienes una cuenta? <a href="/forotodo/php-login/login.php">Inicia sesión</a>
        </div>
        <div class="btn d-flex gap-2 justify-content-center border mt-3 shadow-sm">
            <img src="/forotodo/assets/img/google.png" alt="" style="height: 1.6rem;">
            <div class="fw-semibold">Google</div>
        </div>
    </div>

</body>

</html>