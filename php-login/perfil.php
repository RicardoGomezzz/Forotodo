<?php
session_start();

require '../php-login/db.php';

$username = null;
$email = null;

if (isset($_SESSION['user_id'])) {
  $records = $conn->prepare('SELECT id, user, email FROM users WHERE id = :id');
  $records->bindParam(':id', $_SESSION['user_id']);
  $records->execute();
  $results = $records->fetch(PDO::FETCH_ASSOC);

  if (count($results) > 0) {
    $username = $results['user'];
    $email = $results['email'];
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil de Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/forotodo/assets/css/perfil.css">
</head>
<body>

<?php include 'partials/nav.php'; ?>

<div class="container mt-5">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title text-center">Mi Perfil</h4>
      <div class="row mt-4">
        <div class="col-md-3 align-self-start custom-position-left">
          <div class="d-flex flex-column align-items-center text-center p-3 py-5">
            <img class="rounded-circle mt-5" width="150px" src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
            <span class="font-weight-bold">Edogaru</span>
            <span class="text-black-50">edogaru@mail.com.my</span>
            <span></span>
            <button class="btn btn-primary mt-3" id="editPhotoBtn">Editar Foto</button>
          </div>
        </div>
        <div class="col-md-6 col-lg-6 justify-content-end custom-position-right">
          <form>
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" placeholder="Nombre" value="">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label">Nombre de Usuario</label>
                  <input type="text" class="form-control" value="" placeholder="Nombre de Usuario">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" placeholder="Email" value="">
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <label class="form-label">Contraseña</label>
                  <input type="password" class="form-control" placeholder="Contraseña" value="">
                </div>
              </div>
            </div>
            <div class="text-end">
              <button class="btn btn-primary" type="button">Guardar Perfil</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript para el botón de editar foto -->
<script>
  document.getElementById("editPhotoBtn").addEventListener("click", function() {
    // Lógica para editar la foto de perfil
    alert("Editar foto de perfil");
  });
</script>

</body>
</html>
