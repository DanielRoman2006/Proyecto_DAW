<?php
session_start();
include 'conexion.php';

// Solo permitir acceso si el usuario es administrador
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

$matricula = isset($_GET['matricula']) ? $_GET['matricula'] : '';
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'];
    $nueva_contra = $_POST['nueva_contra'];
    $admin_contra = $_POST['admin_contra'];

    $admin_id = $_SESSION['admin_id'];
    $consulta_admin = "SELECT `contraseña` AS current_pass FROM usuarios WHERE matricula = '" . mysqli_real_escape_string($conn, $admin_id) . "' AND bloqueado = 0";
    $resultado_admin = mysqli_query($conn, $consulta_admin);
    if ($row_admin = mysqli_fetch_assoc($resultado_admin)) {
        $stored = $row_admin['current_pass'];
        if ((function_exists('password_verify') && password_verify($admin_contra, $stored)) || $admin_contra === $stored) {
            $hash = password_hash($nueva_contra, PASSWORD_DEFAULT);
            $update = "UPDATE usuarios SET `contraseña` = '" . mysqli_real_escape_string($conn, $hash) . "' WHERE matricula = '" . mysqli_real_escape_string($conn, $matricula) . "'";
            if (mysqli_query($conn, $update)) {
                $mensaje = '<div class="alert alert-success">Contraseña actualizada correctamente para el usuario.</div>';
            } else {
                $mensaje = '<div class="alert alert-danger">Error al actualizar la contraseña.</div>';
            }
        } else {
            $mensaje = '<div class="alert alert-danger">Contraseña de administrador incorrecta.</div>';
        }
    } else {
        $mensaje = '<div class="alert alert-danger">No se pudo validar al administrador.</div>';
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Cambiar contraseña de usuario</h2>
        <?php echo $mensaje; ?>
        <form method="post" class="p-4 bg-white rounded shadow" style="max-width: 400px; margin:auto;">
            <div class="mb-3">
                <label class="form-label">Matrícula del usuario</label>
                <input type="text" class="form-control" name="matricula" value="<?php echo htmlspecialchars($matricula); ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del usuario</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombre); ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" class="form-control" name="nueva_contra" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Tu contraseña de administrador</label>
                <input type="password" class="form-control" name="admin_contra" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Actualizar contraseña</button>
            <a href="gestionUsuarios.php" class="btn btn-secondary w-100 mt-2">Regresar</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
