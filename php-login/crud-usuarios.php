<?php
session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require 'db.php';

// Obtener el nombre de usuario actual de la sesión
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Obtener todos los usuarios no administradores de la base de datos
$stmt = $conn->query('SELECT id, user, email FROM users WHERE admin = 0 ORDER BY id');
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar la eliminación de un usuario
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Verificar que el ID del usuario a eliminar no sea el del administrador actual
    if ($id == $_SESSION['user_id']) {
        header('Location: crud-usuarios.php');
        exit;
    }
    
    // Eliminar el usuario de la base de datos
    $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = :id');
    $deleteStmt->bindParam(':id', $id);
    $deleteStmt->execute();
    
    header('Location: crud-usuarios.php');
    exit;
}

// Procesar la modificación de un usuario
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $user = $_POST['user'];
    $email = $_POST['email'];
    
    // Actualizar los datos del usuario en la base de datos
    $updateStmt = $conn->prepare('UPDATE users SET user = :user, email = :email WHERE id = :id');
    $updateStmt->bindParam(':user', $user);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':id', $id);
    $updateStmt->execute();
    
    header('Location: crud-usuarios.php');
    exit;
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/forotodo/assets/css/usuarios.css" rel="stylesheet" type="text/css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>

<body>

    <?php include 'partials/nav.php'; ?>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Tabla de Usuarios</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Email</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <th scope="row"><?php echo $usuario['id']; ?></th>
                            <td><?php echo $usuario['user']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td>
                                <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>"
                                    class="btn btn-sm btn-info"><i class="fi fi-rr-pen-square"></i></a>
                                <a href="?delete=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger"><i
                                        class="fi fi-rr-delete-user"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>