<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    // Usamos clases bootstrap para el mensaje de error de acceso
    echo "<div class='container mt-5'><div class='alert alert-danger text-center' role='alert'>⛔ Acceso denegado. Solo administradores.</div></div>";
    exit();
}

include 'encabezadoadmin.php';

if (isset($_POST['actualizar_estado']) && isset($_POST['id_pedido']) && isset($_POST['nuevo_estado'])) {
    $id_pedido = mysqli_real_escape_string($conn, $_POST['id_pedido']);
    $nuevo_estado = mysqli_real_escape_string($conn, $_POST['nuevo_estado']);

    $updateQuery = "UPDATE pedidos SET estado_pedido = '$nuevo_estado' WHERE id_pedido = '$id_pedido'";
    if (mysqli_query($conn, $updateQuery)) {
        // Enviar correo de notificación de cambio de estado
        include 'EnviarCorreo.php';
        $correo_enviado = enviarCorreoCambioEstado($id_pedido, $nuevo_estado);
        
        header("Location: paginaAdmin.php?success=cambiado");
        exit();
    } else {
        header("Location: paginaAdmin.php?error=actualizar");
        exit();
    }
}

$mensaje = '';
$tipo_mensaje = '';

if (isset($_GET['success'])) {
    $tipo_mensaje = 'success';
    if ($_GET['success'] === 'cambiado') {
        $mensaje = "✏️ Estado del pedido actualizado correctamente";
    }
}

if (isset($_GET['error'])) {
    $tipo_mensaje = 'danger'; // Bootstrap usa 'danger' para errores
    if ($_GET['error'] === 'actualizar') {
        $mensaje = "❌ Error al actualizar el estado del pedido";
    }
}

// Parámetros de búsqueda
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conn, $_GET['buscar']) : '';
$estado_filtro = isset($_GET['estado']) ? mysqli_real_escape_string($conn, $_GET['estado']) : '';
$fecha_filtro = isset($_GET['fecha']) ? mysqli_real_escape_string($conn, $_GET['fecha']) : '';

// Construir la consulta con filtros
$whereConditions = [];

if (!empty($buscar)) {
    $whereConditions[] = "(p.numero_orden LIKE '%$buscar%' OR p.matricula LIKE '%$buscar%' OR pr.nombre LIKE '%$buscar%')";
}

if (!empty($estado_filtro)) {
    $whereConditions[] = "p.estado_pedido = '$estado_filtro'";
}

if (!empty($fecha_filtro)) {
    $whereConditions[] = "DATE(p.fecha_hora_pedido) = '$fecha_filtro'";
}


$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

$queryPedidos = "SELECT p.*, 
    GROUP_CONCAT(CONCAT(pr.nombre, ' (x', dp.cantidad, ')') SEPARATOR ', ') as productos,
    GROUP_CONCAT(CASE WHEN dp.nota_detalle != '' THEN CONCAT(pr.nombre, ': ', dp.nota_detalle) END SEPARATOR ' | ') as notas_productos
    FROM pedidos p 
    LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido 
    LEFT JOIN productos pr ON dp.id_producto = pr.id_producto 
    $whereClause 
    GROUP BY p.id_pedido 
    ORDER BY p.fecha_hora_pedido DESC";

$resultadoPedidos = mysqli_query($conn, $queryPedidos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Pedidos Pendientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="estilo.css">
  
  <style>
      /* Pequeños ajustes personalizados para mantener los colores de tu marca */
      .bg-orange-subtle { background-color: #fff3e0; }
      .btn-orange { background-color: #e67e22; color: white; border: none; }
      .btn-orange:hover { background-color: #ca6b1e; color: white; }
      .text-orange { color: #bf360c; }
      .card-hover:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transition: all 0.3s; }
  </style>
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
        <a href="gestionarProductos.php" class="btn btn-orange btn-lg shadow-sm">Gestionar Productos</a>
        <a href="agregarU.php" class="btn btn-orange btn-lg shadow-sm">Agregar Usuario</a>
        <a href="gestionUsuarios.php" class="btn btn-orange btn-lg shadow-sm">Gestionar Usuarios</a>
    </div>

    <div class="card shadow-sm border-warning mb-5 bg-orange-subtle">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end search-form">
                <div class="col-md-5 search-field">
                    <label for="buscar" class="form-label fw-bold">🔍 Buscar:</label>
                    <input type="text" class="form-control border-warning" id="buscar" name="buscar" 
                           value="<?= htmlspecialchars($buscar) ?>" 
                           placeholder="Orden, matrícula o producto...">
                </div>
                
                <div class="col-md-3 search-field">
                    <label for="estado" class="form-label fw-bold">🕒 Estado:</label>
                    <select id="estado" name="estado" class="form-select border-warning">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?= $estado_filtro === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en_preparacion" <?= $estado_filtro === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
                        <option value="listo" <?= $estado_filtro === 'listo' ? 'selected' : '' ?>>Listo</option>
                        <option value="entregado" <?= $estado_filtro === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                        <option value="cancelado" <?= $estado_filtro === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-2 search-field">
                    <label for="fecha" class="form-label fw-bold">📅 Fecha:</label>
                    <input type="date" class="form-control border-warning" id="fecha" name="fecha" 
                           value="<?= htmlspecialchars($fecha_filtro) ?>">
                </div>
                
                <div class="col-md-2 search-buttons d-flex gap-2">
                    <button type="submit" class="btn btn-orange w-100">🔍 Buscar</button>
                    <a href="paginaAdmin.php" class="btn btn-secondary w-auto" title="Limpiar">🗑️</a>
                </div>
            </form>

            <?php 
            // Mostrar estadísticas de búsqueda
            $total_pedidos = mysqli_num_rows($resultadoPedidos);
            $filtros_activos = [];
            if (!empty($buscar)) $filtros_activos[] = "búsqueda: '$buscar'";
            if (!empty($estado_filtro)) $filtros_activos[] = "estado: " . ucfirst(str_replace('_', ' ', $estado_filtro));
            if (!empty($fecha_filtro)) $filtros_activos[] = "fecha: $fecha_filtro";
            ?>
            
            <div class="search-stats text-center mt-3 text-muted fw-medium">
                📊 Mostrando <?= $total_pedidos ?> pedido(s)
                <?php if (!empty($filtros_activos)): ?>
                    <small>(<?= implode(', ', $filtros_activos) ?>)</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 class="text-center text-orange mb-4 fw-bold">
        📋 Pedidos 
        <?php 
            if (!empty($estado_filtro)) {
                echo ucfirst(str_replace('_', ' ', $estado_filtro));
            } elseif (!empty($buscar) || !empty($fecha_filtro)) {
                echo "coincidentes";
            } else {
                echo "pendientes";
            }
        ?>
    </h2>

    <?php if (!empty($mensaje)): ?>
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show shadow-sm" role="alert" id="mensaje">
                <?= $mensaje ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="cerrarMensaje()"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 pedidos-container">
    <?php
    if ($resultadoPedidos && mysqli_num_rows($resultadoPedidos) > 0) {
        while ($pedido = mysqli_fetch_assoc($resultadoPedidos)) {
            // Determinamos color de borde según estado (opcional, mejora visual)
            $bordeClase = 'border-warning'; 
    ?>
        <div class="col pedido-card"> 
            <div class="card h-100 shadow-sm <?= $bordeClase ?> card-hover bg-orange-subtle">
                <div class="card-header bg-warning bg-opacity-50 text-center fw-bold border-bottom border-warning">
                    Pedido #<?= htmlspecialchars($pedido['numero_orden']) ?>
                </div>
                
                <div class="card-body pedido-info text-dark">
                    <p class="card-text mb-1"><strong>👤 Matrícula:</strong> <?= htmlspecialchars($pedido['matricula']) ?></p>
                    <p class="card-text mb-1"><strong>📅 Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])) ?></p>
                    <p class="card-text mb-1"><strong>🕒 Estado:</strong> <span class="badge bg-secondary"><?= ucfirst($pedido['estado_pedido']) ?></span></p>
                    <p class="card-text mb-1"><strong>💰 Total:</strong> $<?= number_format($pedido['total'], 2) ?></p>
                    <hr class="my-2 border-warning">
                    <p class="card-text mb-1"><strong>🍽️ Productos:</strong> <?= htmlspecialchars($pedido['productos'] ?: 'Sin productos') ?></p>
                    
                    <?php if (!empty($pedido['notas_productos'])): ?>
                        <p class="card-text mb-1 text-primary"><small><strong>📝 Notas prod:</strong> <?= htmlspecialchars($pedido['notas_productos']) ?></small></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($pedido['notas'])): ?>
                        <div class="alert alert-warning p-2 mt-2 mb-0"><small><strong>📋 Notas:</strong> <?= htmlspecialchars($pedido['notas']) ?></small></div>
                    <?php endif; ?>
                    
                    <?php if ($pedido['sancionado'] == 1): ?>
                        <div class="alert alert-danger p-1 mt-2 text-center"><strong>⚠️ Sancionado</strong></div>
                    <?php endif; ?>
                </div>

                <div class="card-footer bg-transparent border-0 pb-3">
                    <form method="POST" class="d-grid gap-2 pedido-actions">
                        <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
                        <select name="nuevo_estado" class="form-select form-select-sm border-success">
                            <?php
                            $estados = ['pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado'];
                            foreach ($estados as $estado) {
                                $selected = $pedido['estado_pedido'] === $estado ? 'selected' : '';
                                echo "<option value=\"$estado\" $selected>" . ucfirst($estado) . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="actualizar_estado" class="btn btn-success btn-sm">
                            ✏️ Cambiar Estado
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php
        }
    } else {
    ?>
        <div class="col-12 no-results-original">
            <?php if (!empty($buscar) || !empty($estado_filtro) || !empty($fecha_filtro)) { ?>
                <div class="alert alert-info text-center">🔍 No se encontraron pedidos que coincidan con los filtros aplicados</div>
            <?php } else { ?>
                <div class="alert alert-success text-center">🎉 No hay pedidos pendientes</div>
            <?php } ?>
        </div>
    <?php
    }
    ?>
    </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// He conservado tu lógica JS original, solo adaptándola a las clases de Bootstrap cuando es necesario

function cerrarMensaje() {
    // Bootstrap ya maneja el cierre con data-bs-dismiss, pero mantenemos esto para el timer
    const mensaje = document.getElementById('mensaje');
    if (mensaje) {
        // Usamos la instancia de alerta de Bootstrap para cerrarla suavemente si queremos forzarlo por JS
        var bsAlert = new bootstrap.Alert(mensaje);
        bsAlert.close();
        
        // Limpiar URL
        if (window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
  const mensaje = document.getElementById('mensaje');
  if (mensaje) {
    setTimeout(() => {
      cerrarMensaje();
    }, 5000);
  }
  
  // Funcionalidad del buscador
  const searchForm = document.querySelector('.search-form');
  const searchInput = document.getElementById('buscar');
  const estadoSelect = document.getElementById('estado');
  const fechaInput = document.getElementById('fecha');
  
  // Búsqueda en tiempo real (con delay para evitar muchas peticiones)
  let searchTimeout;
  if(searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          filtrarPedidos();
        }, 500);
      });
  }
  
  // Filtrado inmediato para selects
  if(estadoSelect) estadoSelect.addEventListener('change', filtrarPedidos);
  if(fechaInput) fechaInput.addEventListener('change', filtrarPedidos);
  
  function filtrarPedidos() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const estadoFiltro = estadoSelect ? estadoSelect.value : '';
    const fechaFiltro = fechaInput ? fechaInput.value : '';
    
    // Seleccionamos los elementos que tienen la clase 'pedido-card' que está en la columna
    const pedidoCards = document.querySelectorAll('.pedido-card');
    let pedidosVisibles = 0;
    
    pedidoCards.forEach(card => {
      // Buscamos dentro de la card el contenido de texto
      const textoCard = card.textContent.toLowerCase();
      
      // Ajuste para buscar el estado dentro de las etiquetas badge o texto
      // Buscamos la clase 'pedido-info' dentro de la columna actual
      const infoDiv = card.querySelector('.pedido-info');
      const infoText = infoDiv ? infoDiv.textContent : '';
      
      // Extracción simple basada en tu lógica anterior, pero adaptada a la estructura Bootstrap
      // Como el texto está ahí, includes funciona bien.
      
      let mostrar = true;
      
      // Filtrar por texto de búsqueda
      if (searchTerm && !textoCard.includes(searchTerm)) {
        mostrar = false;
      }
      
      // Filtrar por estado (buscamos si el valor del select está en el texto de la tarjeta)
      if (estadoFiltro) {
         // Normalizamos un poco para comparar
         const estadoBusqueda = estadoFiltro.replace('_', ' ').toLowerCase();
         if (!textoCard.includes(estadoBusqueda)) {
             mostrar = false;
         }
      }
      
      // Filtrar por fecha (Buscamos el formato dd/mm/YYYY en la tarjeta)
      if (fechaFiltro) {
        const fechaFormateada = fechaFiltro.split('-').reverse().join('/'); // Convierte YYYY-MM-DD a DD/MM/YYYY
        if (!textoCard.includes(fechaFormateada)) {
             mostrar = false;
        }
      }
      
      if (mostrar) {
        // En Bootstrap grid, usamos d-block o d-none en el contenedor de columna
        card.style.display = 'block'; 
        pedidosVisibles++;
      } else {
        card.style.display = 'none';
      }
    });
    
    // Actualizar estadísticas
    updateSearchStats(pedidosVisibles);
    
    // Mostrar mensaje si no hay resultados
    const container = document.querySelector('.pedidos-container');
    const noResults = container.querySelector('.no-results-js'); // Clase específica para el msg de JS
    
    // Ocultamos el mensaje original de PHP si estamos filtrando con JS
    const originalNoResults = container.querySelector('.no-results-original');
    if(originalNoResults) originalNoResults.style.display = 'none';

    if (pedidosVisibles === 0 && !noResults) {
      const noResultsMsg = document.createElement('div');
      noResultsMsg.className = 'col-12 no-results-js';
      noResultsMsg.innerHTML = '<div class="alert alert-warning text-center mt-3">🔍 No se encontraron pedidos que coincidan con los filtros aplicados</div>';
      container.appendChild(noResultsMsg);
    } else if (pedidosVisibles > 0 && noResults) {
      noResults.remove();
    }
  }
  
  function updateSearchStats(visible) {
    const statsDiv = document.querySelector('.search-stats');
    if (statsDiv) {
      const filtros = [];
      if (searchInput && searchInput.value) filtros.push(`búsqueda: '${searchInput.value}'`);
      if (estadoSelect && estadoSelect.value) filtros.push(`estado: ${estadoSelect.options[estadoSelect.selectedIndex].text}`);
      if (fechaInput && fechaInput.value) filtros.push(`fecha: ${fechaInput.value}`);
      
      let statsText = `📊 Mostrando ${visible} pedido(s)`;
      if (filtros.length > 0) {
        statsText += ` con filtros: ${filtros.join(', ')}`;
      }
      statsDiv.textContent = statsText;
    }
  }
  
  // Limpiar búsqueda con tecla Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if(searchInput) searchInput.value = '';
      if(estadoSelect) estadoSelect.value = '';
      if(fechaInput) fechaInput.value = '';
      filtrarPedidos();
    }
  });
  
  // Auto-focus
  if(searchInput) searchInput.focus();
  
  // Atajos de teclado
  document.addEventListener('keydown', function(e) {
    // Ctrl + F
    if (e.ctrlKey && e.key === 'f') {
      e.preventDefault();
      if(searchInput) {
          searchInput.focus();
          searchInput.select();
      }
    }
    
    // Ctrl + R
    if (e.ctrlKey && e.key === 'r') {
      e.preventDefault();
      window.location.href = 'paginaAdmin.php';
    }
  });
});
</script>

</body>
</html>

<?php include 'pie.html'; ?>