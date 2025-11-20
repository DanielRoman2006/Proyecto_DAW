<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Uso de hashing de contraseñas (Recomendación de seguridad: si bien tu código no lo usa,
    // es crucial usar `password_verify` y `password_hash` en entornos reales)
    
    if (empty($current) || empty($new) || empty($confirm)) {
        $error = 'Completa todos los campos.';
    } elseif ($new !== $confirm) {
        $error = 'La nueva contraseña y la confirmación no coinciden.';
    } elseif (strlen($new) < 4) {
        $error = 'La contraseña debe tener al menos 4 caracteres.';
    } else {
        if ($role === 'user' && isset($_SESSION['matricula'])) {
            $mat = $_SESSION['matricula'];
            $stmt = $conn->prepare('SELECT contraseña FROM usuarios WHERE matricula = ? LIMIT 1');
            $stmt->bind_param('s', $mat);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if ($row['contraseña'] === $current) { // Comparación directa (sin hash)
                    $upd = $conn->prepare('UPDATE usuarios SET contraseña = ? WHERE matricula = ?');
                    $upd->bind_param('ss', $new, $mat);
                    if ($upd->execute()) {
                        $message = 'Contraseña actualizada correctamente.';
                    } else {
                        $error = 'Error al actualizar la contraseña.';
                    }
                } else {
                    $error = 'Contraseña actual incorrecta.';
                }
            } else {
                $error = 'Usuario no encontrado.';
            }
        } elseif ($role === 'admin' && isset($_SESSION['id_admin'])) {
            $id = $_SESSION['id_admin'];
            $stmt = $conn->prepare('SELECT contraseña FROM administradores WHERE id_admin = ? LIMIT 1');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if ($row['contraseña'] === $current) { // Comparación directa (sin hash)
                    $upd = $conn->prepare('UPDATE administradores SET contraseña = ? WHERE id_admin = ?');
                    $upd->bind_param('si', $new, $id);
                    if ($upd->execute()) {
                        $message = 'Contraseña actualizada correctamente.';
                    } else {
                        $error = 'Error al actualizar la contraseña.';
                    }
                } else {
                    $error = 'Contraseña actual incorrecta.';
                }
            } else {
                $error = 'Administrador no encontrado.';
            }
        } else {
            $error = 'Rol inválido o sesión incompleta.';
        }
    }
}

// ----------------------------------------------------
// INCLUSIÓN CONDICIONAL DEL ENCABEZADO
// ----------------------------------------------------
if ($role === 'admin') {
    include 'encabezadoAdmin.php';
} else {
    // Si es 'user' u otro rol autenticado, usa el encabezado de sesión estándar
    include 'encabezado_con_sesion.php';
}
?>

<style>
    :root{--guinda:#4C0000;--naranja:#FF8F00}
    .card{
        border-radius:12px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .card .card-title{
        color:var(--guinda);
        font-weight:700;
    }
    .btn-naranja{background:var(--naranja);border-color:var(--naranja);color:#fff}
    .btn-naranja:hover{background:#e67a00;border-color:#e67a00}
    .btn-guinda{background:var(--guinda);border-color:var(--guinda);color:#fff}
    .btn-outline-guinda{color:var(--guinda);border-color:var(--guinda)}
    .btn-outline-guinda:hover{background:rgba(76,0,0,0.08)}
    body { background: linear-gradient(180deg, rgba(76,0,0,0.04), rgba(255,143,0,0.02)); }
    .form-label{color:#4a2c0f}
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between bg-white border-0">
                    <div>
                        <h5 class="mb-0" style="color:var(--guinda);font-weight:700">Cambiar contraseña</h5>
                        <small class="text-muted">Actualiza tu contraseña de forma segura</small>
                    </div>
                    <!-- Nota: Se recomienda evitar rutas relativas como 'imagenes/UpQroo.jpg'
                         ya que el archivo puede ser accedido desde diferentes niveles. -->
                    <img src="imagenes/UpQroo.jpg" alt="UpQroo" style="height:44px;border-radius:6px;object-fit:cover">
                </div>
                <div class="card-body">

                    <?php if ($message): ?>
                        <div class="alert alert-success" role="alert"><?=htmlspecialchars($message)?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert"><?=htmlspecialchars($error)?></div>
                    <?php endif; ?>

                    <form method="post" autocomplete="off">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <div class="input-group">
                                <input id="current_password" type="password" name="current_password" class="form-control" required>
                                <button class="btn btn-outline-guinda" type="button" onclick="toggleVisibility('current_password')">Mostrar</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input id="new_password" type="password" name="new_password" class="form-control" required>
                                <button class="btn btn-outline-guinda" type="button" onclick="toggleVisibility('new_password')">Mostrar</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmar nueva contraseña</label>
                            <div class="input-group">
                                <input id="confirm_password" type="password" name="confirm_password" class="form-control" required>
                                <button class="btn btn-outline-guinda" type="button" onclick="toggleVisibility('confirm_password')">Mostrar</button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-naranja">Actualizar contraseña</button>
                            <a href="profile.php" class="btn btn-outline-guinda">Volver al perfil</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleVisibility(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.type = el.type === 'password' ? 'text' : 'password';
}
</script>

<?php include 'pie.html'; ?>