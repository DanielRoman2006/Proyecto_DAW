<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$info = [];

if ($role === 'user' && isset($_SESSION['matricula'])) {
    $mat = $_SESSION['matricula'];
    $stmt = $conn->prepare('SELECT matricula, nombre, correo, fecha_registro FROM usuarios WHERE matricula = ? LIMIT 1');
    $stmt->bind_param('s', $mat);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $info = $res->fetch_assoc();
    }
} elseif ($role === 'admin' && isset($_SESSION['id_admin'])) {
    $id = $_SESSION['id_admin'];
    $stmt = $conn->prepare('SELECT id_admin, usuario, nombre, correo, nivel, fecha_registro FROM administradores WHERE id_admin = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $info = $res->fetch_assoc();
    }
}
?>
<?php include 'encabezado.html'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Perfil</h4>
                    <?php if (empty($info)): ?>
                        <div class="alert alert-warning">No se encontró información del perfil.</div>
                    <?php else: ?>
                        <table class="table">
                            <tbody>
                                <?php foreach ($info as $key => $val): ?>
                                    <tr>
                                        <th style="text-transform:capitalize; width:35%;"><?=htmlspecialchars($key)?></th>
                                        <td><?=htmlspecialchars($val)?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="d-flex gap-2">
                            <a href="cambiar_contrasena.php" class="btn btn-warning">Cambiar contraseña</a>
                            <a href="cerrar_sesion.php" class="btn btn-outline-secondary">Cerrar sesión</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'pie.html'; ?>
