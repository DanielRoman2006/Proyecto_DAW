<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center' role='alert'><i class='bi bi-x-octagon-fill'></i> Acceso denegado. Solo administradores.</div></div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
    $precio = mysqli_real_escape_string($conn, $_POST['precio']);
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    
    $imagen_url = '';
    
    if ($_POST['tipo_imagen'] === 'subir' && isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === 0) {
        $archivo = $_FILES['imagen_archivo'];
        
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($archivo['type'], $tiposPermitidos)) {
            
            if ($archivo['size'] <= 5 * 1024 * 1024) {
                
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = 'producto_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                $rutaDestino = 'imagenes/' . $nombreArchivo;
                
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    $imagen_url = $rutaDestino;
                } else {
                    header("Location: agregarProducto.php?error=subir_archivo");
                    exit();
                }
            } else {
                header("Location: agregarProducto.php?error=archivo_grande");
                exit();
            }
        } else {
            header("Location: agregarProducto.php?error=formato_archivo");
            exit();
        }
    } else {
        $imagen_url = mysqli_real_escape_string($conn, $_POST['imagen_url']);
    }
    
    $insertQuery = "INSERT INTO productos (nombre, descripcion, precio, tipo, imagen_url, disponible) 
                    VALUES ('$nombre', '$descripcion', '$precio', '$tipo', '$imagen_url', '$disponible')";
    
    if (mysqli_query($conn, $insertQuery)) {
        header("Location: gestionarProductos.php?success=agregado");
        exit();
    } else {
        header("Location: agregarProducto.php?error=agregar");
        exit();
    }
}
?>

<?php include 'encabezadoadmin.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="estilo.css">
    <style>
        .form-container {
            background-color: #fff3e0;
            padding: 2rem;
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(255,111,0,0.3);
        }

        .form-title {
            text-align: center;
            color: #bf360c;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }

        .required {
            color: #f44336;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #4a2c0f;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .btn-secondary {
            background-color: #ff6f00;
            border-color: #ff6f00;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #e65100;
            border-color: #e65100;
        }
        
        /* Estilos para el input file */
        .form-control-file {
            background-color: #fff8f0 !important;
            border: 2px dashed #ff6f00 !important;
            padding: 1rem !important;
            text-align: center;
            cursor: pointer;
        }

        .form-control-file:hover {
            border-color: #e65100 !important;
            background-color: #fff3e0 !important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="form-container">
        <div class="text-center">
            <a href="gestionarProductos.php" class="back-link btn btn-sm btn-outline-warning mb-4">
                <i class="bi bi-arrow-left"></i> Volver a Gestión de Productos
            </a>
        </div>
        
        <h1 class="form-title">
            <i class="bi bi-plus-circle-fill me-2"></i> Agregar Nuevo Producto
        </h1>
        
        <form method="POST" id="productForm" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Producto <span class="required">*</span></label>
                <input type="text" name="nombre" id="nombre" class="form-control" required 
                        placeholder="Ej: Hamburguesa Especial">
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción <span class="required">*</span></label>
                <textarea name="descripcion" id="descripcion" class="form-control" required 
                            placeholder="Describe el producto, ingredientes, tamaño, etc." rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label">Precio <span class="required">*</span></label>
                <input type="number" name="precio" id="precio" class="form-control" step="0.01" min="0" required 
                        placeholder="0.00">
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Producto <span class="required">*</span></label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="">Selecciona el tipo</option>
                    <option value="comida"><i class="bi bi-egg-fried"></i> Comida</option>
                    <option value="bebida"><i class="bi bi-cup-straw"></i> Bebida</option>
                    <option value="promo"><i class="bi bi-gift-fill"></i> Promoción</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen del Producto <span class="required">*</span></label>
                
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_imagen" value="subir" id="subir" checked onchange="cambiarTipoImagen()">
                        <label class="form-check-label" for="subir"><i class="bi bi-cloud-arrow-up-fill"></i> Subir imagen desde mi computadora</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_imagen" value="url" id="url" onchange="cambiarTipoImagen()">
                        <label class="form-check-label" for="url"><i class="bi bi-globe"></i> Usar URL de imagen</label>
                    </div>
                </div>
                
                <div id="subirArchivo">
                    <input type="file" name="imagen_archivo" accept="image/*" onchange="previewUploadedImage()" 
                            class="form-control form-control-file">
                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF (máximo 5MB)</small>
                </div>
                
                <div id="urlImagen" style="display: none;">
                    <input type="url" name="imagen_url" id="imagen_url_input" class="form-control" 
                            placeholder="https://ejemplo.com/imagen.jpg" onchange="previewUrlImage()">
                </div>
                
                <div class="preview-container text-center mt-3" id="imagePreview" style="display: none;">
                    <p class="mb-2 fw-bold">Vista previa:</p>
                    <img id="previewImg" class="preview-image img-fluid rounded border border-secondary p-1" alt="Vista previa">
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="disponible" id="disponible" checked>
                    <label class="form-check-label fw-bold" for="disponible">
                        <i class="bi bi-patch-check-fill text-success"></i> Producto disponible (activo)
                    </label>
                </div>
                <small class="form-text text-muted">Si no está marcado, el producto estará desactivado</small>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">
                    <i class="bi bi-eraser-fill"></i> Limpiar
                </button>
                <button type="submit" name="agregar_producto" class="btn btn-primary">
                    <i class="bi bi-check-circle-fill"></i> Agregar Producto
                </button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function cambiarTipoImagen() {
            const subir = document.getElementById('subir').checked;
            document.getElementById('subirArchivo').style.display = subir ? 'block' : 'none';
            document.getElementById('urlImagen').style.display = subir ? 'none' : 'block';
            
            document.getElementById('imagePreview').style.display = 'none';
        }

        function previewUploadedImage() {
            const file = document.querySelector('input[type="file"]').files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('El archivo es demasiado grande. Máximo 5MB permitido.');
                    document.querySelector('input[type="file"]').value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert('Formato no válido. Solo se permiten: JPG, PNG, GIF');
                    document.querySelector('input[type="file"]').value = '';
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

        function previewUrlImage() {
            const imageUrl = document.getElementById('imagen_url_input').value;
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (imageUrl) {
                previewImg.src = imageUrl;
                previewImg.onerror = function() {
                    preview.style.display = 'none';
                    alert('La URL de la imagen no es válida o no se puede cargar');
                };
                previewImg.onload = function() {
                    preview.style.display = 'block';
                };
            } else {
                preview.style.display = 'none';
            }
        }

        function limpiarFormulario() {
            if (confirm('¿Estás seguro de limpiar todos los campos?')) {
                document.getElementById('productForm').reset();
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('subir').checked = true;
                cambiarTipoImagen();
            }
        }

        document.getElementById('productForm').addEventListener('submit', function(e) {
            const precio = parseFloat(document.getElementById('precio').value);
            
            if (precio <= 0) {
                e.preventDefault();
                alert('El precio debe ser mayor a 0');
                return false;
            }
            
            const tipoImagen = document.querySelector('input[name="tipo_imagen"]:checked').value;
            
            if (tipoImagen === 'subir') {
                const archivo = document.querySelector('input[type="file"]').files[0];
                if (!archivo) {
                    e.preventDefault();
                    alert('Por favor selecciona una imagen para subir');
                    return false;
                }
            } else {
                const imageUrl = document.getElementById('imagen_url_input').value;
                if (!imageUrl) {
                    e.preventDefault();
                    alert('Por favor ingresa una URL de imagen');
                    return false;
                }
            }
        });
        
        document.addEventListener('DOMContentLoaded', cambiarTipoImagen);
    </script>
</body>
</html>

<?php include 'pie.html'; ?>