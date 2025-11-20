<?php
session_start();
include 'conexion.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "<p class='text-danger text-center mt-3'><i class='bi bi-x-octagon-fill'></i> Acceso denegado. Solo administradores.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cambiar_disponibilidad'])) {
        $id_producto = mysqli_real_escape_string($conn, $_POST['id_producto']);
        $nueva_disponibilidad = mysqli_real_escape_string($conn, $_POST['nueva_disponibilidad']);

        $updateQuery = "UPDATE productos SET disponible = '$nueva_disponibilidad' WHERE id_producto = '$id_producto'";

        if (mysqli_query($conn, $updateQuery)) {
            $mensaje = $nueva_disponibilidad == 1 ? "activado" : "desactivado";
            header("Location: gestionarProductos.php?success=disponibilidad&estado=$mensaje");
            exit();
        } else {
            header("Location: gestionarProductos.php?error=disponibilidad");
            exit();
        }
    }

    if (isset($_POST['eliminar_producto'])) {
        $id_producto = mysqli_real_escape_string($conn, $_POST['id_producto']);

        $deleteQuery = "DELETE FROM productos WHERE id_producto = '$id_producto'";

        if (mysqli_query($conn, $deleteQuery)) {
            header("Location: gestionarProductos.php?success=eliminado");
            exit();
        } else {
            header("Location: gestionarProductos.php?error=eliminar");
            exit();
        }
    }

    if (isset($_POST['actualizar_producto'])) {
        $id_producto = mysqli_real_escape_string($conn, $_POST['id_producto']);
        $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
        $precio = mysqli_real_escape_string($conn, $_POST['precio']);

        $imagen_url = '';

        if ($_POST['tipo_imagen_edit'] === 'subir' && isset($_FILES['imagen_archivo_edit']) && $_FILES['imagen_archivo_edit']['error'] === 0) {
            $archivo = $_FILES['imagen_archivo_edit'];

            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($archivo['type'], $tiposPermitidos)) {

                if ($archivo['size'] <= 5 * 1024 * 1024) {

                    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                    $nombreArchivo = 'producto_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                    $rutaDestino = 'imagenes/' . $nombreArchivo;

                    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                        $imagen_url = $rutaDestino;
                    } else {
                        header("Location: gestionarProductos.php?error=subir_archivo");
                        exit();
                    }
                } else {
                    header("Location: gestionarProductos.php?error=archivo_grande");
                    exit();
                }
            } else {
                header("Location: gestionarProductos.php?error=formato_archivo");
                exit();
            }
        } else {
            $imagen_url = mysqli_real_escape_string($conn, $_POST['imagen_url']);
        }

        $updateQuery = "UPDATE productos SET
                                 nombre = '$nombre',
                                 descripcion = '$descripcion',
                                 precio = '$precio',
                                 imagen_url = '$imagen_url'
                                 WHERE id_producto = '$id_producto'";

        if (mysqli_query($conn, $updateQuery)) {
            header("Location: gestionarProductos.php?success=actualizado");
            exit();
        } else {
            header("Location: gestionarProductos.php?error=actualizar");
            exit();
        }
    }
}

$queryProductos = "SELECT * FROM productos ORDER BY nombre ASC";
$resultadoProductos = mysqli_query($conn, $queryProductos);

$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['success'])) {
    $tipo_mensaje = 'success';
    switch ($_GET['success']) {
        case 'disponibilidad':
            $estado = isset($_GET['estado']) ? $_GET['estado'] : 'actualizado';
            $mensaje = "<i class='bi bi-check-circle-fill'></i> Producto $estado correctamente";
            break;
        case 'eliminado':
            $mensaje = "<i class='bi bi-check-circle-fill'></i> Producto eliminado correctamente";
            break;
        case 'actualizado':
            $mensaje = "<i class='bi bi-check-circle-fill'></i> Producto actualizado correctamente";
            break;
        case 'agregado':
            $mensaje = "<i class='bi bi-check-circle-fill'></i> Producto agregado correctamente";
            break;
    }
}

if (isset($_GET['error'])) {
    $tipo_mensaje = 'danger';
    switch ($_GET['error']) {
        case 'disponibilidad':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> Error al cambiar la disponibilidad del producto";
            break;
        case 'eliminar':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> Error al eliminar producto. Puede que esté en uso";
            break;
        case 'actualizar':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> Error al actualizar el producto";
            break;
        case 'subir_archivo':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> Error al subir el archivo de imagen";
            break;
        case 'formato_archivo':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> Formato de archivo no válido. Solo JPG, PNG, GIF";
            break;
        case 'archivo_grande':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> El archivo es demasiado grande. Máximo 5MB";
            break;
        case 'agregar':
            $mensaje = "<i class='bi bi-x-circle-fill'></i> Error al agregar el producto";
            break;
    }
}
?>

<?php include 'encabezadoadmin.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
        .card-producto-custom {
            background-color: #fff3e0;
            transition: transform 0.3s ease;
            color: #4a2c0f;
            border-color: #ffcc80;
        }
        .card-producto-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(255,111,0,0.5) !important;
        }
        .card-producto-custom.no-disponible {
            opacity: 0.6;
            background-color: #f5f5f5;
        }
        .img-container-custom {
            background-color: #ffcc80;
            height: 10rem;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img-container-custom img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
        }
        .section-title {
            color: #bf360c;
        }
        #editModal .modal-content {
            background-color: #fff3e0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="controls-section text-center mb-5">
             <a href="paginaAdmin.php" class="btn btn-warning mb-3">
                <i class="bi bi-house-door-fill"></i> Volver al Panel Admin
            </a>
            <h1 class="section-title fw-bold"><i class="bi bi-bag-fill"></i> Gestión de Productos</h1>
            <a href="agregarProducto.php" class="btn btn-success btn-lg shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Agregar Nuevo Producto
            </a>
        </div>

        <?php if (!empty($mensaje)): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert" id="mensaje">
                    <span><?= $mensaje ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="cerrarMensaje()"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 mb-5">
            <?php
            if ($resultadoProductos && mysqli_num_rows($resultadoProductos) > 0) {
                while ($producto = mysqli_fetch_assoc($resultadoProductos)) {
                    $disponible = $producto['disponible'] == 1;
                    ?>
                    <div class="col d-flex">
                        <div class="card card-producto-custom shadow-sm flex-fill <?= !$disponible ? 'no-disponible' : '' ?>">
                            <div class="img-container-custom">
                                <?php
                                $imagen_corregida = str_replace('/politastehub/imagenes/', 'imagenes/', $producto['imagen_url']);
                                ?>
                                <img src="<?= htmlspecialchars($imagen_corregida) ?>"
                                     class="card-img-top p-2"
                                     alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                     onerror="this.src='imagenes/placeholder.png'; this.onerror=null;" />
                            </div>

                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title fw-bold mb-0"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                    <span class="badge rounded-pill text-bg-<?= $disponible ? 'success' : 'danger' ?>">
                                        <?= $disponible ? '<i class="bi bi-check-circle-fill"></i> Disponible' : '<i class="bi bi-x-circle-fill"></i> No Disponible' ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted small mb-1"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                <p class="card-text fw-bolder fs-5 text-danger">$<?= number_format($producto['precio'], 2) ?></p>
                                <small class="text-secondary mb-3"><strong>Tipo:</strong> <?= ucfirst($producto['tipo']) ?></small>

                                <div class="mt-auto pt-2">
                                    <button class="btn btn-primary w-100 mb-2" onclick="editarProducto(<?= htmlspecialchars(json_encode($producto)) ?>)" data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>

                                    <form method="POST" class="d-flex mb-2">
                                        <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                        <input type="hidden" name="nueva_disponibilidad" value="<?= $disponible ? 0 : 1 ?>">
                                        <button type="submit" name="cambiar_disponibilidad"
                                                class="btn btn-<?= $disponible ? 'warning' : 'success' ?> w-100">
                                            <?= $disponible ? '<i class="bi bi-pause-fill"></i> Desactivar' : '<i class="bi bi-play-fill"></i> Activar' ?>
                                        </button>
                                    </form>

                                    <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')" class="w-100">
                                        <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                        <button type="submit" name="eliminar_producto" class="btn btn-danger w-100">
                                            <i class="bi bi-trash-fill"></i> Eliminar Producto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-secondary fs-5 mt-5"><i class="bi bi-box-seam-fill"></i> No hay productos registrados</p></div>';
            }
            ?>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><i class="bi bi-pencil-square"></i> Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_producto" id="edit_id">

                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre:</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_descripcion" class="form-label">Descripción:</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_precio" class="form-label">Precio:</label>
                            <input type="number" name="precio" id="edit_precio" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagen del Producto:</label>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_imagen_edit" value="url" id="url_edit" checked onchange="cambiarTipoImagenEdit()">
                                <label class="form-check-label" for="url_edit"><i class="bi bi-link-45deg"></i> Usar URL de imagen</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="tipo_imagen_edit" value="subir" id="subir_edit" onchange="cambiarTipoImagenEdit()">
                                <label class="form-check-label" for="subir_edit"><i class="bi bi-camera-fill"></i> Subir nueva imagen</label>
                            </div>

                            <div id="urlImagenEdit" class="mt-2">
                                <input type="text" name="imagen_url" id="edit_imagen" class="form-control" required placeholder="URL de la imagen">
                            </div>

                            <div id="subirArchivoEdit" class="mt-2" style="display: none;">
                                <input type="file" name="imagen_archivo_edit" accept="image/*" onchange="previewUploadedImageEdit()" class="form-control">
                                <small class="text-muted d-block mt-1">Formatos permitidos: JPG, PNG, GIF (máximo 5MB)</small>
                            </div>
                        </div>

                        <div id="imagePreviewEdit" class="text-center mt-3" style="display: none;">
                            <p class="mb-2 fw-bold">Vista previa:</p>
                            <img id="previewImgEdit" class="img-thumbnail" style="max-width: 150px; max-height: 100px;" alt="Vista previa">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="actualizar_producto" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));

        function editarProducto(producto) {
            document.getElementById('edit_id').value = producto.id_producto;
            document.getElementById('edit_nombre').value = producto.nombre;
            document.getElementById('edit_descripcion').value = producto.descripcion;
            document.getElementById('edit_precio').value = producto.precio;

            let imagenCorregida = producto.imagen_url.replace('/politastehub/imagenes/', 'imagenes/');
            document.getElementById('edit_imagen').value = imagenCorregida;

            document.getElementById('url_edit').checked = true;
            cambiarTipoImagenEdit();
            document.getElementById('imagePreviewEdit').style.display = 'none';
        }

        function cambiarTipoImagenEdit() {
            const url = document.getElementById('url_edit').checked;
            document.getElementById('urlImagenEdit').style.display = url ? 'block' : 'none';
            document.getElementById('subirArchivoEdit').style.display = url ? 'none' : 'block';
            document.getElementById('imagePreviewEdit').style.display = 'none';

            if (url) {
                const fileInput = document.querySelector('input[name="imagen_archivo_edit"]');
                if (fileInput) fileInput.value = '';
            }
        }

        function previewUploadedImageEdit() {
            const file = document.querySelector('input[name="imagen_archivo_edit"]').files[0];
            const preview = document.getElementById('imagePreviewEdit');
            const previewImg = document.getElementById('previewImgEdit');

            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. Máximo 5MB permitido.');
                    document.querySelector('input[name="imagen_archivo_edit"]').value = '';
                    preview.style.display = 'none';
                    return;
                }

                const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert('Formato no válido. Solo se permiten: JPG, PNG, GIF');
                    document.querySelector('input[name="imagen_archivo_edit"]').value = '';
                    preview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        document.getElementById('editModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('editForm').reset();
            document.getElementById('imagePreviewEdit').style.display = 'none';
        });

        function cerrarMensaje() {
            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const mensajeElement = document.getElementById('mensaje');
            if (mensajeElement) {
                setTimeout(() => {
                     const alert = bootstrap.Alert.getOrCreateInstance(mensajeElement);
                     alert.close();
                }, 5000);
            }
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            const precio = parseFloat(document.getElementById('edit_precio').value);

            if (precio <= 0) {
                e.preventDefault();
                alert('El precio debe ser mayor a 0');
                return false;
            }

            const tipoImagen = document.querySelector('input[name="tipo_imagen_edit"]:checked').value;

            if (tipoImagen === 'subir') {
                const archivo = document.querySelector('input[name="imagen_archivo_edit"]').files[0];
                if (!archivo && document.getElementById('edit_imagen').value.trim() === '') {
                    e.preventDefault();
                    alert('Por favor selecciona una imagen para subir o usa la URL.');
                    return false;
                }
            } else {
                const imageUrl = document.getElementById('edit_imagen').value;
                if (!imageUrl || imageUrl.trim() === '') {
                    e.preventDefault();
                    alert('Por favor ingresa una URL de imagen');
                    return false;
                }
            }
        });
    </script>
</body>
</html>

<?php include 'pie.html'; ?>