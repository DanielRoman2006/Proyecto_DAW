<?php
session_start();
require_once 'conexion.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ident = trim($_POST['ident'] ?? '');
  $pass = trim($_POST['password'] ?? '');

  $logged = false;
  if ($ident !== '') {
    if (ctype_digit($ident)) {
      $id_admin = intval($ident);
      $stmt = $conn->prepare('SELECT id_admin, usuario, nombre FROM administradores WHERE id_admin = ? AND contraseña = ? LIMIT 1');
      if ($stmt) {
        $stmt->bind_param('is', $id_admin, $pass);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
          $row = $res->fetch_assoc();
          $_SESSION['role'] = 'admin';
          $_SESSION['id_admin'] = $row['id_admin'];
          $_SESSION['usuario'] = $row['usuario'] ?? $row['nombre'];
          header('Location: dashboard_admin.php');
          exit;
        }
      }
    }

    $stmt = $conn->prepare('SELECT matricula, nombre FROM usuarios WHERE matricula = ? AND contraseña = ? LIMIT 1');
    if ($stmt) {
      $stmt->bind_param('ss', $ident, $pass);
      $stmt->execute();
      $res = $stmt->get_result();
      if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $_SESSION['role'] = 'user';
        $_SESSION['matricula'] = $row['matricula'];
        $_SESSION['nombre'] = $row['nombre'];
        header('Location: menu.php');
        exit;
      }
    }
  }

  $error = 'Credenciales incorrectas. Verifica matrícula/ID y contraseña.';
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Politaste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      :root{--guinda:#4C0000;--naranja:#FF8F00}
      html,body{height:100%}
      body{
        margin:0;
        font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;
        background-image: linear-gradient(180deg, rgba(76,0,0,0.72) 0%, rgba(76,0,0,0.18) 35%, rgba(255,143,0,0.12) 65%, rgba(255,143,0,0.72) 100%), url('imagenes/UpQroo.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        display:flex;align-items:center;justify-content:center;padding:24px;
      }
      .card{border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.25);backdrop-filter: blur(4px);background: rgba(255,255,255,0.92);max-width:480px;width:100%}
      .brand-banner{background:transparent;color:var(--guinda);padding:16px 18px 6px 18px;display:flex;align-items:center;gap:12px}
      .brand-banner img{height:56px;border-radius:8px}
      .btn-naranja{background:var(--naranja);border-color:var(--naranja);color:#fff}
      .btn-naranja:hover{background:#e67a00}
      @media (max-width:420px){.brand-banner img{height:44px}}
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-5">
          <div class="card overflow-hidden">
            <div class="brand-banner">
              <img src="imagenes/UpQroo.jpg" alt="UpQroo">
              <div>
                <h5 class="mb-0">Iniciar sesión</h5>
                <small>Politaste - Ingresa como administrador o usuario</small>
              </div>
            </div>
            <div class="card-body p-4">
              <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?=htmlspecialchars($error)?></div>
              <?php endif; ?>

              <form method="post" novalidate>
                <div class="mb-3">
                  <label for="ident" class="form-label">Matrícula o ID</label>
                  <input type="text" class="form-control" id="ident" name="ident" placeholder="ID o Matrícula" required>
                  <div class="form-text">Introduce tu matrícula (usuarios) o tu ID (administradores).</div>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Contraseña</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-naranja">Entrar</button>
                </div>
              </form>

              <div class="mt-3 text-center small text-muted">Si olvidas tu contraseña contacta al administrador del sistema.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
