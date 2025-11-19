<?php
session_start();
include 'conexion.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "<p style='color:red; text-align:center;'>⛔ Acceso denegado. Solo administradores.</p>";
    exit();
}

// Procesar acciones
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
        
        // Determinar si es archivo subido o URL
        if ($_POST['tipo_imagen_edit'] === 'subir' && isset($_FILES['imagen_archivo_edit']) && $_FILES['imagen_archivo_edit']['error'] === 0) {
            // Procesar archivo subido
            $archivo = $_FILES['imagen_archivo_edit'];
            
            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($archivo['type'], $tiposPermitidos)) {
                
                // Validar tamaño (máximo 5MB)
                if ($archivo['size'] <= 5 * 1024 * 1024) {
                    
                    // Generar nombre único para evitar conflictos
                    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                    $nombreArchivo = 'producto_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                    $rutaDestino = 'imagenes/' . $nombreArchivo;
                    
                    // Mover archivo a la carpeta imagenes
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
            // Usar URL proporcionada
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

// Consulta para obtener todos los productos
$queryProductos = "SELECT * FROM productos ORDER BY nombre ASC";
$resultadoProductos = mysqli_query($conn, $queryProductos);

// Procesar mensajes de confirmación
$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['success'])) {
    $tipo_mensaje = 'success';
    switch ($_GET['success']) {
        case 'disponibilidad':
            $estado = isset($_GET['estado']) ? $_GET['estado'] : 'actualizado';
            $mensaje = "✅ Producto $estado correctamente";
            break;
        case 'eliminado':
            $mensaje = "✅ Producto eliminado correctamente";
            break;
        case 'actualizado':
            $mensaje = "✅ Producto actualizado correctamente";
            break;
        case 'agregado':
            $mensaje = "✅ Producto agregado correctamente";
            break;
    }
}

if (isset($_GET['error'])) {
    $tipo_mensaje = 'error';
    switch ($_GET['error']) {
        case 'disponibilidad':
            $mensaje = "❌ Error al cambiar la disponibilidad del producto";
            break;
        case 'eliminar':
            $mensaje = "❌ Error al eliminar producto. Puede que esté en uso";
            break;
        case 'actualizar':
            $mensaje = "❌ Error al actualizar el producto";
            break;
        case 'subir_archivo':
            $mensaje = "❌ Error al subir el archivo de imagen";
            break;
        case 'formato_archivo':
            $mensaje = "❌ Formato de archivo no válido. Solo JPG, PNG, GIF";
            break;
        case 'archivo_grande':
            $mensaje = "❌ El archivo es demasiado grande. Máximo 5MB";
            break;
        case 'agregar':
            $mensaje = "❌ Error al agregar el producto";
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
    <link rel="stylesheet" href="estilo.css">
    <style>
        /* Estilos para las cartas de productos - mismo estilo que menu.php */
        .productos-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .producto-card {
            background-color: #fff3e0;
            border-radius: 1rem;
            width: 18rem;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            box-shadow: 0 4px 8px rgba(255,111,0,0.3);
            transition: transform 0.3s ease;
            color: #4a2c0f;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(255,111,0,0.5);
        }

        .producto-card.no-disponible {
            opacity: 0.6;
            background-color: #f5f5f5;
        }

        .image_container {
            background-color: #ffcc80;
            border-radius: 0.5rem;
            height: 8rem;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image_container img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .producto-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .estado-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .disponible {
            background-color: #4caf50;
            color: white;
        }

        .no-disponible-badge {
            background-color: #f44336;
            color: white;
        }

        .producto-info {
            flex-grow: 1;
        }

        .title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .descripcion {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #bf360c;
        }

        .producto-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: auto;
        }

        .btn-row {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-editar {
            background-color: #2196F3;
            color: white;
        }

        .btn-editar:hover {
            background-color: #1976D2;
        }

        .btn-activar {
            background-color: #4CAF50;
            color: white;
        }

        .btn-activar:hover {
            background-color: #45a049;
        }

        .btn-desactivar {
            background-color: #FF9800;
            color: white;
        }

        .btn-desactivar:hover {
            background-color: #f57c00;
        }

        .btn-eliminar {
            background-color: #f44336;
            color: white;
        }

        .btn-eliminar:hover {
            background-color: #d32f2f;
        }

        .btn-agregar {
            background-color: #4CAF50;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin: 2rem auto;
            display: block;
            transition: all 0.3s ease;
        }

        .btn-agregar:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .section-title {
            text-align: center;
            color: #bf360c;
            margin: 2rem 0 1rem 0;
            font-size: 1.8rem;
        }

        .controls-section {
            text-align: center;
            margin: 2rem 0;
        }

        /* Modal para editar */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff3e0;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
        }

        .modal input, .modal textarea {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        /* Estilos para mensajes de confirmación */
        .mensaje-container {
            max-width: 600px;
            margin: 1rem auto;
            padding: 0 1rem;
        }

        .mensaje {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideDown 0.3s ease-out;
        }

        .mensaje.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mensaje-close {
            margin-left: auto;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.7;
        }

        .mensaje-close:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="controls-section">
         <a href="paginaAdmin.php">
        <button class="btn-regresar">🏠 Volver al Panel Admin</button>
    </a>
        <h1 class="section-title">🛍️ Gestión de Productos</h1>
        <!-- Botón para agregar productos -->
        <a href="agregarProducto.php">
            <button class="btn-agregar">
                ➕ Agregar Nuevo Producto
            </button>
        </a>
    </div>

    <!-- Mensajes de confirmación -->
    <?php if (!empty($mensaje)): ?>
    <div class="mensaje-container">
        <div class="mensaje <?= $tipo_mensaje ?>" id="mensaje">
            <span><?= $mensaje ?></span>
            <span class="mensaje-close" onclick="cerrarMensaje()">&times;</span>
        </div>
    </div>
    <?php endif; ?>

    <div class="productos-container">
        <?php
        if ($resultadoProductos && mysqli_num_rows($resultadoProductos) > 0) {
            while ($producto = mysqli_fetch_assoc($resultadoProductos)) {
                $disponible = $producto['disponible'] == 1;
                ?>
                <div class="producto-card <?= !$disponible ? 'no-disponible' : '' ?>">
                    <div class="producto-header">
                        <span class="estado-badge <?= $disponible ? 'disponible' : 'no-disponible-badge' ?>">
                            <?= $disponible ? '✅ Disponible' : '❌ No Disponible' ?>
                        </span>
                    </div>
                    
                    <div class="image_container">
                        <?php
                        // Corregir ruta de imagen si tiene el formato antiguo
                        $imagen_corregida = str_replace('/politastehub/imagenes/', 'imagenes/', $producto['imagen_url']);
                        ?>
                        <img src="<?= htmlspecialchars($imagen_corregida) ?>" 
                             alt="<?= htmlspecialchars($producto['nombre']) ?>" 
                             onerror="this.src='imagenes/placeholder.png'; this.onerror=null;" />
                    </div>
                    
                    <div class="producto-info">
                        <div class="title"><?= htmlspecialchars($producto['nombre']) ?></div>
                        <div class="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></div>
                        <div class="price">$<?= number_format($producto['precio'], 2) ?></div>
                        <div style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">
                            <strong>Tipo:</strong> <?= ucfirst($producto['tipo']) ?>
                        </div>
                    </div>
                    
                    <div class="producto-actions">
                        <div class="btn-row">
                            <button class="btn btn-editar" onclick="editarProducto(<?= htmlspecialchars(json_encode($producto)) ?>)">
                                ✏️ Editar
                            </button>
                            
                            <form method="POST" style="flex: 1;">
                                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <input type="hidden" name="nueva_disponibilidad" value="<?= $disponible ? 0 : 1 ?>">
                                <button type="submit" name="cambiar_disponibilidad" 
                                        class="btn <?= $disponible ? 'btn-desactivar' : 'btn-activar' ?>">
                                    <?= $disponible ? '⏸️ Desactivar' : '▶️ Activar' ?>
                                </button>
                            </form>
                        </div>
                        
                        <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                            <button type="submit" name="eliminar_producto" class="btn btn-eliminar">
                                🗑️ Eliminar Producto
                            </button>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p style="text-align: center; color: #9e9e9e; font-size: 1.2rem; margin: 2rem;">📦 No hay productos registrados</p>';
        }
        ?>
    </div>

    <!-- Modal para editar producto -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2 style="color: #bf360c; text-align: center;">✏️ Editar Producto</h2>
            
            <form method="POST" id="editForm" enctype="multipart/form-data">
                <input type="hidden" name="id_producto" id="edit_id">
                
                <label for="edit_nombre">Nombre:</label>
                <input type="text" name="nombre" id="edit_nombre" required>
                
                <label for="edit_descripcion">Descripción:</label>
                <textarea name="descripcion" id="edit_descripcion" rows="3" required></textarea>
                
                <label for="edit_precio">Precio:</label>
                <input type="number" name="precio" id="edit_precio" step="0.01" min="0" required>
                
                <label>Imagen del Producto:</label>
                
                <!-- Opciones de tipo de imagen para editar -->
                <div style="margin-bottom: 1rem;">
                    <div style="margin-bottom: 0.5rem;">
                        <input type="radio" name="tipo_imagen_edit" value="url" id="url_edit" checked onchange="cambiarTipoImagenEdit()">
                        <label for="url_edit" style="display: inline; margin-left: 0.5rem;">🌐 Usar URL de imagen</label>
                    </div>
                    <div>
                        <input type="radio" name="tipo_imagen_edit" value="subir" id="subir_edit" onchange="cambiarTipoImagenEdit()">
                        <label for="subir_edit" style="display: inline; margin-left: 0.5rem;">📷 Subir nueva imagen</label>
                    </div>
                </div>
                
                <!-- URL de imagen -->
                <div id="urlImagenEdit">
                    <input type="text" name="imagen_url" id="edit_imagen" required 
                           placeholder="URL de la imagen">
                </div>
                
                <!-- Subir archivo -->
                <div id="subirArchivoEdit" style="display: none;">
                    <input type="file" name="imagen_archivo_edit" accept="image/*" onchange="previewUploadedImageEdit()" 
                           style="margin-bottom: 0.5rem; padding: 0.5rem; border: 2px dashed #ff6f00; background-color: #fff8f0; border-radius: 0.5rem;">
                    <small style="color: #666; display: block;">Formatos permitidos: JPG, PNG, GIF (máximo 5MB)</small>
                </div>
                
                <!-- Vista previa para modal -->
                <div id="imagePreviewEdit" style="display: none; text-align: center; margin-top: 1rem;">
                    <p style="margin-bottom: 0.5rem; font-weight: 600;">Vista previa:</p>
                    <img id="previewImgEdit" style="max-width: 150px; max-height: 100px; border-radius: 0.5rem; border: 2px solid #ddd;" alt="Vista previa">
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn btn-desactivar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" name="actualizar_producto" class="btn btn-editar">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editarProducto(producto) {
            document.getElementById('edit_id').value = producto.id_producto;
            document.getElementById('edit_nombre').value = producto.nombre;
            document.getElementById('edit_descripcion').value = producto.descripcion;
            document.getElementById('edit_precio').value = producto.precio;
            
            // Corregir ruta de imagen si tiene formato antiguo y cargar en URL por defecto
            let imagenCorregida = producto.imagen_url.replace('/politastehub/imagenes/', 'imagenes/');
            document.getElementById('edit_imagen').value = imagenCorregida;
            
            // Resetear opciones del modal
            document.getElementById('url_edit').checked = true;
            cambiarTipoImagenEdit();
            document.getElementById('imagePreviewEdit').style.display = 'none';
            
            document.getElementById('editModal').style.display = 'block';
        }

        function cambiarTipoImagenEdit() {
            const url = document.getElementById('url_edit').checked;
            document.getElementById('urlImagenEdit').style.display = url ? 'block' : 'none';
            document.getElementById('subirArchivoEdit').style.display = url ? 'none' : 'block';
            
            // Limpiar vista previa al cambiar
            document.getElementById('imagePreviewEdit').style.display = 'none';
            
            // Limpiar archivo seleccionado si se cambia a URL
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
                // Validar tamaño (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. Máximo 5MB permitido.');
                    document.querySelector('input[name="imagen_archivo_edit"]').value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                // Validar tipo
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

        function cerrarModal() {
            document.getElementById('editModal').style.display = 'none';
            // Limpiar formulario del modal
            document.getElementById('editForm').reset();
            document.getElementById('imagePreviewEdit').style.display = 'none';
        }

        function cerrarMensaje() {
            const mensaje = document.getElementById('mensaje');
            if (mensaje) {
                mensaje.style.opacity = '0';
                mensaje.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    mensaje.parentElement.remove();
                    // Limpiar la URL sin recargar la página
                    if (window.history.replaceState) {
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                }, 300);
            }
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Auto-cerrar mensaje después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const mensaje = document.getElementById('mensaje');
            if (mensaje) {
                setTimeout(() => {
                    cerrarMensaje();
                }, 5000);
            }
        });

        // Validación del formulario de edición
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const precio = parseFloat(document.getElementById('edit_precio').value);
            
            if (precio <= 0) {
                e.preventDefault();
                alert('El precio debe ser mayor a 0');
                return false;
            }
            
            // Validar que se haya proporcionado una imagen
            const tipoImagen = document.querySelector('input[name="tipo_imagen_edit"]:checked').value;
            
            if (tipoImagen === 'subir') {
                const archivo = document.querySelector('input[name="imagen_archivo_edit"]').files[0];
                if (!archivo) {
                    e.preventDefault();
                    alert('Por favor selecciona una imagen para subir');
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

<?php include 'pie.php'; ?>
