<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "<p style='color:red; text-align:center;'>⛔ Acceso denegado. Solo administradores.</p>";
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
    $tipo_mensaje = 'error';
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
  <title>Pedidos Pendientes</title>
  <link rel="stylesheet" href="estilo.css">
  <style>
    ul {
  list-style: none;
  padding: 0;
  margin: 30px auto;
  max-width: 400px;
  background-color: #fff3e0;
  border-radius: 12px;
  box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

li {
  margin: 15px 0;
}

a {
  text-decoration: none;
  display: block;
}

button {
  width: 100%;
  background-color: #e67e22;
  color: white;
  padding: 12px 20px;
  font-size: 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
}

button:hover {
  background-color: #ca6b1e;
}
/* Estilos para las cartas de pedidos - mismo estilo que menu.php */
.pedidos-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  margin: 2rem auto;
  max-width: 1200px;
}

.pedido-card {
  background-color: #fff3e0;
  border-radius: 1rem;
  width: 20rem;
  min-height: 16rem;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  box-shadow: 0 4px 8px rgba(255,111,0,0.3);
  transition: transform 0.3s ease;
  color: #4a2c0f;
}

.pedido-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(255,111,0,0.5);
}

.pedido-header {
  background-color: #ffcc80;
  border-radius: 0.5rem;
  padding: 0.75rem;
  text-align: center;
  font-weight: 600;
}

.pedido-info {
  font-size: 0.9rem;
  line-height: 1.4;
}

.pedido-actions {
  display: flex;
  gap: 10px;
  margin-top: auto;
}

.btn-entregar {
  flex: 1;
  background: linear-gradient(0deg, #4caf50 50%, #fff7f0 125%);
  border: 2px solid rgba(76,175,80,0.5);
  border-radius: 0.5rem;
  color: #2e7d32;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-entregar:hover {
  background-color: #2e7d32;
  color: white;
}

/* Estilos específicos para select en las tarjetas de pedidos */
.pedido-actions select {
  width: 100% !important;
  padding: 8px !important;
  font-size: 14px !important;
  border-radius: 6px !important;
  border: 1px solid #ccc !important;
  margin-bottom: 8px !important;
  background-color: white !important;
}

.section-title {
  text-align: center;
  color: #bf360c;
  margin: 2rem 0 1rem 0;
  font-size: 1.8rem;
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

/* Estilos para el buscador */
.search-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 1.5rem;
  background-color: #fff3e0;
  border-radius: 1rem;
  box-shadow: 0 4px 8px rgba(255,111,0,0.3);
}

.search-form {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr auto;
  gap: 1rem;
  align-items: end;
}

.search-field {
  display: flex;
  flex-direction: column;
}

.search-field label {
  font-weight: 600;
  color: #4a2c0f;
  margin-bottom: 0.5rem;
}

.search-field input,
.search-field select {
  padding: 0.75rem !important;
  border: 2px solid #ffcc80 !important;
  border-radius: 0.5rem !important;
  font-size: 1rem !important;
  background-color: white !important;
  transition: border-color 0.3s ease !important;
  width: 100% !important;
  box-sizing: border-box !important;
  margin: 0 !important;
}

.search-field input:focus,
.search-field select:focus {
  outline: none !important;
  border-color: #e67e22 !important;
}

.search-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-search {
  background: linear-gradient(0deg, #e67e22 50%, #fff7f0 125%);
  color: white;
  border: 2px solid rgba(230,126,34,0.5);
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
}

.btn-search:hover {
  background-color: #bf5d1a;
  transform: translateY(-2px);
}

.btn-clear {
  background: linear-gradient(0deg, #95a5a6 50%, #fff7f0 125%);
  color: white;
  border: 2px solid rgba(149,165,166,0.5);
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  text-decoration: none;
}

.btn-clear:hover {
  background-color: #7f8c8d;
  transform: translateY(-2px);
}

.search-stats {
  margin-top: 1rem;
  text-align: center;
  color: #4a2c0f;
  font-weight: 500;
}

@media (max-width: 768px) {
  .search-form {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .search-buttons {
    justify-content: center;
  }
}
  </style>
</head>
<body>

<ul>
  <a href="gestionarProductos.php"><li><button>Gestionar Productos</button></li></a>
  <a href="agregarU.php"><li><button>Agregar Usuario</button></li></a>
  <a href="gestionUsuarios.php"><li><button>Gestionar Usuarios</button></li></a>
</ul>

<!-- Buscador de pedidos -->
<div class="search-container">
  <form method="GET" class="search-form">
    <div class="search-field">
      <label for="buscar">🔍 Buscar por número de orden, matrícula o producto:</label>
      <input type="text" 
             id="buscar" 
             name="buscar" 
             value="<?= htmlspecialchars($buscar) ?>" 
             placeholder="Ejemplo: 12345, A01234567, hamburguesa...">
    </div>
    
    <div class="search-field">
      <label for="estado">🕒 Filtrar por estado:</label>
      <select id="estado" name="estado">
        <option value="">Todos los estados</option>
        <option value="pendiente" <?= $estado_filtro === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="en_preparacion" <?= $estado_filtro === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
        <option value="listo" <?= $estado_filtro === 'listo' ? 'selected' : '' ?>>Listo</option>
        <option value="entregado" <?= $estado_filtro === 'entregado' ? 'selected' : '' ?>>Entregado</option>
        <option value="cancelado" <?= $estado_filtro === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
      </select>
    </div>
    
    <div class="search-field">
      <label for="fecha">📅 Filtrar por fecha:</label>
      <input type="date" 
             id="fecha" 
             name="fecha" 
             value="<?= htmlspecialchars($fecha_filtro) ?>">
    </div>
    
    <div class="search-buttons">
      <button type="submit" class="btn-search">🔍 Buscar</button>
      <a href="paginaAdmin.php" class="btn-clear">🗑️ Limpiar</a>
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
  
  <div class="search-stats">
    📊 Mostrando <?= $total_pedidos ?> pedido(s)
    <?php if (!empty($filtros_activos)): ?>
      con filtros: <?= implode(', ', $filtros_activos) ?>
    <?php endif; ?>
  </div>
</div>

<h2 class="section-title">
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
<div class="mensaje-container">
  <div class="mensaje <?= $tipo_mensaje ?>" id="mensaje">
    <span><?= $mensaje ?></span>
    <span class="mensaje-close" onclick="cerrarMensaje()">&times;</span>
  </div>
</div>
<?php endif; ?>

<div class="pedidos-container">
<?php
if ($resultadoPedidos && mysqli_num_rows($resultadoPedidos) > 0) {
  while ($pedido = mysqli_fetch_assoc($resultadoPedidos)) {
?>
    <div class="pedido-card">
      <div class="pedido-header">Pedido #<?= htmlspecialchars($pedido['numero_orden']) ?></div>
      <div class="pedido-info">
        <strong>👤 Matrícula:</strong> <?= htmlspecialchars($pedido['matricula']) ?><br>
        <strong>📅 Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])) ?><br>
        <strong>🕒 Estado:</strong> <?= ucfirst($pedido['estado_pedido']) ?><br>
        <strong>💰 Total:</strong> $<?= number_format($pedido['total'], 2) ?><br>
        <strong>🍽️ Productos:</strong> <?= htmlspecialchars($pedido['productos'] ?: 'Sin productos') ?><br>
        <?php if (!empty($pedido['notas_productos'])): ?>
          <strong>📝 Notas productos:</strong> <?= htmlspecialchars($pedido['notas_productos']) ?><br>
        <?php endif; ?>
        <?php if (!empty($pedido['notas'])): ?>
          <strong>📋 Notas generales:</strong> <?= htmlspecialchars($pedido['notas']) ?><br>
        <?php endif; ?>
        <?php if ($pedido['sancionado'] == 1): ?>
          <strong>⚠️ Sancionado:</strong> <span style="color: red;">Sí</span>
        <?php endif; ?>
      </div>
      <div class="pedido-actions">
        <form method="POST">
          <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">
          <select name="nuevo_estado">
            <?php
            $estados = ['pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado'];
            foreach ($estados as $estado) {
              $selected = $pedido['estado_pedido'] === $estado ? 'selected' : '';
              echo "<option value=\"$estado\" $selected>" . ucfirst($estado) . "</option>";
            }
            ?>
          </select>
          <button type="submit" name="actualizar_estado" class="btn-entregar">✏️ Cambiar Estado</button>
        </form>
      </div>
    </div>
<?php
  }
} else {
  if (!empty($buscar) || !empty($estado_filtro) || !empty($fecha_filtro)) {
    echo '<p style="text-align: center; color: #666; font-size: 1.2rem; margin: 2rem 0;">🔍 No se encontraron pedidos que coincidan con los filtros aplicados</p>';
  } else {
    echo '<p style="text-align: center;">🎉 No hay pedidos pendientes</p>';
  }
}
?>
</div>

<script>
function cerrarMensaje() {
  const mensaje = document.getElementById('mensaje');
  if (mensaje) {
    mensaje.style.opacity = '0';
    mensaje.style.transform = 'translateY(-20px)';
    setTimeout(() => {
      mensaje.parentElement.remove();
      if (window.history.replaceState) {
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    }, 300);
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
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      filtrarPedidos();
    }, 500);
  });
  
  // Filtrado inmediato para selects
  estadoSelect.addEventListener('change', filtrarPedidos);
  fechaInput.addEventListener('change', filtrarPedidos);
  
  function filtrarPedidos() {
    const searchTerm = searchInput.value.toLowerCase();
    const estadoFiltro = estadoSelect.value;
    const fechaFiltro = fechaInput.value;
    const pedidoCards = document.querySelectorAll('.pedido-card');
    let pedidosVisibles = 0;
    
    pedidoCards.forEach(card => {
      const textoCard = card.textContent.toLowerCase();
      const estadoCard = card.querySelector('.pedido-info').textContent.match(/estado:\s*(\w+)/i);
      const fechaCard = card.querySelector('.pedido-info').textContent.match(/fecha:\s*(\d{2}\/\d{2}\/\d{4})/);
      
      let mostrar = true;
      
      // Filtrar por texto de búsqueda
      if (searchTerm && !textoCard.includes(searchTerm)) {
        mostrar = false;
      }
      
      // Filtrar por estado
      if (estadoFiltro && estadoCard && estadoCard[1].toLowerCase().replace(' ', '_') !== estadoFiltro) {
        mostrar = false;
      }
      
      // Filtrar por fecha
      if (fechaFiltro && fechaCard) {
        const fechaCardFormatted = fechaCard[1].split('/').reverse().join('-');
        if (fechaCardFormatted !== fechaFiltro) {
          mostrar = false;
        }
      }
      
      if (mostrar) {
        card.style.display = 'flex';
        pedidosVisibles++;
      } else {
        card.style.display = 'none';
      }
    });
    
    // Actualizar estadísticas
    updateSearchStats(pedidosVisibles);
    
    // Mostrar mensaje si no hay resultados
    const container = document.querySelector('.pedidos-container');
    const noResults = container.querySelector('.no-results');
    
    if (pedidosVisibles === 0 && !noResults) {
      const noResultsMsg = document.createElement('p');
      noResultsMsg.className = 'no-results';
      noResultsMsg.style.cssText = 'text-align: center; color: #666; font-size: 1.2rem; margin: 2rem 0;';
      noResultsMsg.innerHTML = '🔍 No se encontraron pedidos que coincidan con los filtros aplicados';
      container.appendChild(noResultsMsg);
    } else if (pedidosVisibles > 0 && noResults) {
      noResults.remove();
    }
  }
  
  function updateSearchStats(visible) {
    const statsDiv = document.querySelector('.search-stats');
    if (statsDiv) {
      const filtros = [];
      if (searchInput.value) filtros.push(`búsqueda: '${searchInput.value}'`);
      if (estadoSelect.value) filtros.push(`estado: ${estadoSelect.options[estadoSelect.selectedIndex].text}`);
      if (fechaInput.value) filtros.push(`fecha: ${fechaInput.value}`);
      
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
      searchInput.value = '';
      estadoSelect.value = '';
      fechaInput.value = '';
      filtrarPedidos();
    }
  });
  
  // Auto-focus en el campo de búsqueda
  searchInput.focus();
  
  // Atajos de teclado
  document.addEventListener('keydown', function(e) {
    // Ctrl + F para enfocar búsqueda
    if (e.ctrlKey && e.key === 'f') {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
    
    // Ctrl + R para limpiar filtros
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