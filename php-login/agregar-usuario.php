<?php
require 'db.php';

session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$userError = $emailError = $passwordError = $nombreError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
  
    // Validar que al menos un campo esté completo
    if (empty($user) && empty($nombre) && empty($email) && empty($password)) {
        $message = 'Completa todos los campos del formulario.';
    } else {
        // Validar cada campo individualmente
        if (empty($user)) {
            $userError = 'Por favor, ingresa un nombre de usuario.';
        }
        if (empty($nombre)) {
            $nombreError = 'Por favor, ingresa un nombre.';
        }
        if (empty($email)) {
            $emailError = 'Por favor, ingresa una dirección de correo electrónico.';
        }
        if (empty($password)) {
            $passwordError = 'Por favor, ingresa una contraseña.';
        }
        
        // Verificar si hay mensajes de error, si no los hay, continuar con la inserción del usuario
        if (empty($userError) && empty($nombreError) && empty($emailError) && empty($passwordError)) {
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
                // Validar la longitud de la contraseña
                if (strlen($password) < 8) {
                    $passwordError = 'La contraseña debe tener al menos 8 caracteres.';
                } else {
                    // Si no hay errores, insertar el nuevo usuario en la base de datos
                    $insertQuery = "INSERT INTO users (user, nombre, email, password) VALUES (:user, :nombre, :email, :password)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bindParam(':user', $user);
                    $insertStmt->bindParam(':nombre', $nombre);
                    $insertStmt->bindParam(':email', $email);
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $insertStmt->bindParam(':password', $hashedPassword);
                    $insertStmt->execute();

                    header('Location: crud-usuarios.php');
                    exit;
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
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/card.css" rel="stylesheet" type="text/css">
</head>

<body>
    <header>
        <?php include 'partials/nav.php'; ?>
    </header>

    <div class="container">
        <div class="custom-container bg-white p-5 rounded-5 shadow mx-auto" style="max-width: 30rem;">
            <div class="text-center fw-bold" id="title">Agregar Usuario</div>
            <div class="text-center my-3">
                <?php if (!empty($message)): ?>
                <div class="alert <?= ($messageClass === 'success') ? 'alert-success' : 'alert-danger'; ?>"
                    role="alert">
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
                <?php if (!empty($nombreError)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $nombreError ?>
                </div>
                <?php endif; ?>
            </div>

            <form action="/forotodo/php-login/agregar-usuario.php" method="POST">
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
                            <img src="/forotodo/assets/img/nombre.png" alt="nombre-icon" style="height: 1rem;">
                        </span>
                        <input class="form-control" type="text" name="nombre" placeholder="Nombre"
                            value="<?= $nombre ?? '' ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <img src="/forotodo/assets/img/email.png" alt="email-icon" style="height: 1rem;">
                        </span>
                        <input class="form-control" type="email" name="email" placeholder="Correo electrónico"
                            value="<?= $email ?? '' ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <img src="/forotodo/assets/img/pass.png" alt="pass-icon" style="height: 1rem;">
                        </span>
                        <input class="form-control" type="password" name="password" placeholder="Contraseña">
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" value="send" class="btn text-white w-100 mt-4 fw-semibold"
                        style="background-color: cadetblue">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</body>




</html>