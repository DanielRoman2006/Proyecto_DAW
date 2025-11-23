<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center' role='alert'><i class='bi bi-x-octagon-fill'></i> Acceso denegado. Solo administradores.</div></div>";
    exit();
}

$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conn, $_GET['buscar']) : '';
$estado_filtro = isset($_GET['estado']) ? mysqli_real_escape_string($conn, $_GET['estado']) : '';
$fecha_filtro = isset($_GET['fecha']) ? mysqli_real_escape_string($conn, $_GET['fecha']) : '';

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
    

if (!$resultadoPedidos) {
    include 'encabezadoAdmin.php';
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger' role='alert'>";
    echo "<h2><i class='bi bi-bug-fill'></i> Error de Consulta SQL</h2>";
    echo "<p><strong>Error MySQL:</strong> " . mysqli_error($conn) . "</p>";
    echo "</div>";
    echo "</div>";
    exit();
}

include 'encabezadoAdmin.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Historial de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="estilo.css">
    
    <style>
        .bg-orange-subtle { background-color: #fff3e0; }
        .btn-orange { background-color: #e67e22; color: white; border: none; }
        .btn-orange:hover { background-color: #ca6b1e; color: white; }
        .text-orange { color: #bf360c; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transition: all 0.3s; }
        .badge.bg-success, .badge.bg-danger, .badge.bg-warning { font-weight: bold; }
        .badge-pendiente { background-color: #ffc107; color: #343a40; }
        .badge-en_preparacion { background-color: #0d6efd; }
        .badge-listo { background-color: #198754; }
        .badge-entregado { background-color: #6c757d; }
        .badge-cancelado { background-color: #dc3545; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
        <a href="paginaAdmin.php" class="btn btn-warning btn-lg shadow-sm"><i class="bi bi-hourglass-split"></i> Pedidos Pendientes</a>
        <a href="gestionarProductos.php" class="btn btn-orange btn-lg shadow-sm"><i class="bi bi-bag-fill"></i> Gestionar Productos</a>
        <a href="agregarU.php" class="btn btn-orange btn-lg shadow-sm"><i class="bi bi-person-plus-fill"></i> Agregar Usuario</a>
        <a href="gestionUsuarios.php" class="btn btn-orange btn-lg shadow-sm"><i class="bi bi-people-fill"></i> Gestionar Usuarios</a>
    </div>

    <div class="card shadow-sm border-warning mb-5 bg-orange-subtle">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end search-form">
                <div class="col-md-5 search-field">
                    <label for="buscar" class="form-label fw-bold"><i class="bi bi-search"></i> Buscar:</label>
                    <input type="text" class="form-control border-warning" id="buscar" name="buscar" 
                            value="<?= htmlspecialchars($buscar) ?>" 
                            placeholder="Orden, matrícula o producto...">
                </div>
                
                <div class="col-md-3 search-field">
                    <label for="estado" class="form-label fw-bold"><i class="bi bi-tag-fill"></i> Estado:</label>
                    <select id="estado" name="estado" class="form-select border-warning">
                        <option value="">Todos los pedidos</option>
                        <option value="pendiente" <?= $estado_filtro === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en_preparacion" <?= $estado_filtro === 'en_preparacion' ? 'selected' : '' ?>>En preparación</option>
                        <option value="listo" <?= $estado_filtro === 'listo' ? 'selected' : '' ?>>Listo</option>
                        <option value="entregado" <?= $estado_filtro === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                        <option value="cancelado" <?= $estado_filtro === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-2 search-field">
                    <label for="fecha" class="form-label fw-bold"><i class="bi bi-calendar-date-fill"></i> Fecha:</label>
                    <input type="date" class="form-control border-warning" id="fecha" name="fecha" 
                            value="<?= htmlspecialchars($fecha_filtro) ?>">
                </div>
                
                <div class="col-md-2 search-buttons d-flex gap-2">
                    <button type="submit" class="btn btn-orange w-100"><i class="bi bi-search"></i> Buscar</button>
                    <a href="historial_pedidos.php" class="btn btn-secondary w-auto" title="Limpiar"><i class="bi bi-trash-fill"></i></a>
                </div>
            </form>

            <?php 
            $total_pedidos = mysqli_num_rows($resultadoPedidos);
            $filtros_activos = [];
            if (!empty($buscar)) $filtros_activos[] = "búsqueda: '$buscar'";
            if (!empty($estado_filtro)) $filtros_activos[] = "estado: " . ucfirst(str_replace('_', ' ', $estado_filtro));
            if (!empty($fecha_filtro)) $filtros_activos[] = "fecha: $fecha_filtro";
            ?>
            
            <div class="search-stats text-center mt-3 text-muted fw-medium">
                <i class="bi bi-bar-chart-fill"></i> Mostrando **<?= $total_pedidos ?>** pedido(s)
                <?php if (!empty($filtros_activos)): ?>
                    <small>(<?= implode(', ', $filtros_activos) ?>)</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 class="text-center text-orange mb-4 fw-bold">
        <i class="bi bi-clock-history"></i> Historial de Pedidos
    </h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 pedidos-container">
    <?php
    if ($resultadoPedidos && mysqli_num_rows($resultadoPedidos) > 0) {
        while ($pedido = mysqli_fetch_assoc($resultadoPedidos)) {
            $estado_clase = str_replace('_', '-', $pedido['estado_pedido']);
            $bordeClase = 'border-secondary';
            if ($pedido['estado_pedido'] === 'entregado') $bordeClase = 'border-success';
            if ($pedido['estado_pedido'] === 'cancelado') $bordeClase = 'border-danger';
            if ($pedido['estado_pedido'] === 'pendiente') $bordeClase = 'border-warning';
    ?>
        <div class="col pedido-card"> 
            <div class="card h-100 shadow-sm <?= $bordeClase ?> card-hover bg-light">
                <div class="card-header bg-<?= $estado_clase == 'cancelado' ? 'danger' : ($estado_clase == 'entregado' ? 'success' : 'warning') ?> bg-opacity-10 text-center fw-bold border-bottom <?= $bordeClase ?>">
                    Pedido #<?= htmlspecialchars($pedido['numero_orden']) ?>
                </div>
                
                <div class="card-body pedido-info text-dark">
                    <p class="card-text mb-1"><strong><i class="bi bi-person-fill"></i> Matrícula:</strong> <?= htmlspecialchars($pedido['matricula']) ?></p>
                    <p class="card-text mb-1"><strong><i class="bi bi-calendar-check-fill"></i> Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])) ?></p>
                    <p class="card-text mb-1"><strong><i class="bi bi-clock-fill"></i> Estado:</strong> <span class="badge badge-<?= $estado_clase ?>"><?= ucfirst(str_replace('_', ' ', $pedido['estado_pedido'])) ?></span></p>
                    <p class="card-text mb-1"><strong><i class="bi bi-currency-dollar"></i> Total:</strong> $<?= number_format($pedido['total'], 2) ?></p>
                    <hr class="my-2 <?= $bordeClase ?>">
                    <p class="card-text mb-1"><strong><i class="bi bi-cup-hot-fill"></i> Productos:</strong> <?= htmlspecialchars($pedido['productos'] ?: 'Sin productos') ?></p>
                    
                    <?php if (!empty($pedido['notas_productos'])): ?>
                        <p class="card-text mb-1 text-primary"><small><strong><i class="bi bi-chat-text-fill"></i> Notas prod:</strong> <?= htmlspecialchars($pedido['notas_productos']) ?></small></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($pedido['notas'])): ?>
                        <div class="alert alert-warning p-2 mt-2 mb-0"><small><strong><i class="bi bi-clipboard-fill"></i> Notas:</strong> <?= htmlspecialchars($pedido['notas']) ?></small></div>
                    <?php endif; ?>
                    
                    <?php if ($pedido['sancionado'] == 1): ?>
                        <div class="alert alert-danger p-1 mt-2 text-center"><strong><i class="bi bi-exclamation-triangle-fill"></i> Usuario Sancionado</strong></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
        }
    } else {
    ?>
        <div class="col-12 no-results-original">
            <div class="alert alert-info text-center"><i class="bi bi-search"></i> No se encontraron pedidos en el historial que coincidan con los filtros aplicados</div>
        </div>
    <?php
    }
    ?>
    </div>
</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('buscar');
    const estadoSelect = document.getElementById('estado');
    const fechaInput = document.getElementById('fecha');
    const container = document.querySelector('.pedidos-container');
    
    function filtrarPedidos() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const estadoFiltro = estadoSelect ? estadoSelect.value : '';
        const fechaFiltro = fechaInput ? fechaInput.value : '';
        
        const pedidoCards = document.querySelectorAll('.pedido-card');
        let pedidosVisibles = 0;
        
        pedidoCards.forEach(card => {
            const textoCard = card.textContent.toLowerCase();
            const infoDiv = card.querySelector('.pedido-info');
            const infoText = infoDiv ? infoDiv.textContent : '';
            const estadoEnTarjeta = card.querySelector('.badge').textContent.trim().toLowerCase().replace(' ', '_');
            
            let mostrar = true;
            
            if (searchTerm && !textoCard.includes(searchTerm)) {
                mostrar = false;
            }
            
            if (estadoFiltro && estadoEnTarjeta !== estadoFiltro) {
                mostrar = false;
            }

            if (fechaFiltro) {
                const fechaEnTarjeta = infoText.match(/\d{2}\/\d{2}\/\d{4}/);
                if (fechaEnTarjeta) {
                    const fechaFormateada = fechaEnTarjeta[0].split('/').reverse().join('-');
                    if (fechaFormateada !== fechaFiltro) {
                        mostrar = false;
                    }
                }
            }

            if (mostrar) {
                card.style.display = 'block'; 
                pedidosVisibles++;
            } else {
                card.style.display = 'none';
            }
        });
        
        updateSearchStats(pedidosVisibles);
        
        let noResults = container.querySelector('.no-results-js');
        const originalNoResults = container.querySelector('.no-results-original');
        if(originalNoResults) originalNoResults.style.display = 'none';

        if (pedidosVisibles === 0 && !noResults) {
            const noResultsMsg = document.createElement('div');
            noResultsMsg.className = 'col-12 no-results-js';
            noResultsMsg.innerHTML = '<div class="alert alert-warning text-center mt-3"><i class="bi bi-search"></i> No se encontraron pedidos que coincidan con los filtros aplicados</div>';
            container.appendChild(noResultsMsg);
        } else if (pedidosVisibles > 0 && noResults) {
            noResults.remove();
        }
    }

    function updateSearchStats(visible) {
        const statsDiv = document.querySelector('.search-stats');
        if (statsDiv) {
            let statsText = `<i class="bi bi-bar-chart-fill"></i> Mostrando <strong>${visible}</strong> pedido(s)`;
            
            const filtros = [];
            if (searchInput && searchInput.value) filtros.push(`búsqueda: '${searchInput.value}'`);
            if (estadoSelect && estadoSelect.value) filtros.push(`estado: ${estadoSelect.options[estadoSelect.selectedIndex].text}`);
            if (fechaInput && fechaInput.value) filtros.push(`fecha: ${fechaInput.value}`);
            
            if (filtros.length > 0) {
                statsText += ` <small>(${filtros.join(', ')})</small>`;
            }
            statsDiv.innerHTML = statsText;
        }
    }
    
    let searchTimeout;
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filtrarPedidos();
            }, 500);
        });
    }
    
    if(estadoSelect) estadoSelect.addEventListener('change', filtrarPedidos);
    if(fechaInput) fechaInput.addEventListener('change', filtrarPedidos);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if(searchInput) searchInput.value = '';
            if(estadoSelect) estadoSelect.value = '';
            if(fechaInput) fechaInput.value = '';
            filtrarPedidos();
        }
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            if(searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            window.location.href = 'historial_pedidos.php';
        }
    });
});
</script>

</body>
</html>

<?php include 'pie.html'; ?>
