<?php
session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require 'db.php';

// Obtener todos los usuarios de la base de datos
$stmt = $conn->query('SELECT id, user, email FROM users');
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
</head>
<body>

<?php include 'partials/nav.php'; ?>

<div class="container">
  <h2>Tabla de Usuarios</h2>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Email</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $usuario): ?>
      <tr>
        <td><?php echo $usuario['id']; ?></td>
        <td><?php echo $usuario['user']; ?></td>
        <td><?php echo $usuario['email']; ?></td>
        <td>
          <a href="editar-usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-primary">Editar</a>
          <a href="?delete=<?php echo $usuario['id']; ?>" class="btn btn-danger">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
