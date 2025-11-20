<?php
session_start();
include 'encabezadoAdmin.php'; 
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_resena'])) {
    $id_resena = $_POST['id_resena'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM reseñas WHERE id_reseña = ?");
        $stmt->bind_param("i", $id_resena);
        
        if ($stmt->execute()) {
            $mensaje_exito = "Reseña eliminada correctamente.";
        } else {
            $mensaje_error = "Error al eliminar la reseña.";
        }
        $stmt->close();
    } catch (Exception $e) {
        $mensaje_error = "Error al procesar la eliminación: " . $e->getMessage();
    }
}

$stats = [
    'total' => 0,
    'promedio' => 0,
    'por_estrella' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]
];

$result = $conn->query("SELECT calificación, COUNT(*) as cantidad FROM reseñas GROUP BY calificación");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stats['por_estrella'][$row['calificación']] = $row['cantidad'];
        $stats['total'] += $row['cantidad'];
    }
}

if ($stats['total'] > 0) {
    $suma_total = 0;
    foreach ($stats['por_estrella'] as $estrella => $cantidad) {
        $suma_total += $estrella * $cantidad;
    }
    $stats['promedio'] = round($suma_total / $stats['total'], 1);
}

$todas_resenas = [];
$query = "SELECT r.id_reseña, r.matricula, r.calificación, r.comentario, r.fecha, u.nombre 
          FROM reseñas r 
          LEFT JOIN usuarios u ON r.matricula = u.matricula 
          ORDER BY r.fecha DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $todas_resenas[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reseñas - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-title {
            text-align: center;
            color: #ff6f00;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 2rem;
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stats-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(255, 111, 0, 0.1);
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: #fff7f0;
            border-radius: 8px;
            border: 2px solid #ff6f00;
        }

        .stat-number {
            font-size: 2.5rem;
            color: #ff6f00;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            color: #666;
            margin-top: 0.5rem;
        }

        .rating-breakdown {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .rating-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .rating-stars {
            color: #ff6f00;
            font-size: 1.2rem;
            min-width: 120px;
        }

        .rating-bar {
            flex: 1;
            height: 20px;
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
        }

        .rating-fill {
            height: 100%;
            background: #ff6f00;
            transition: width 0.3s ease;
        }

        .rating-count {
            min-width: 50px;
            text-align: right;
            color: #666;
        }

        .reviews-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(255, 111, 0, 0.1);
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .reviews-title {
            color: #ff6f00;
            font-size: 1.8rem;
            margin: 0;
        }

        .reviews-count {
            color: #666;
            font-size: 1.1rem;
        }

        .review-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: #fafafa;
            transition: box-shadow 0.3s ease;
        }

        .review-card:hover {
            box-shadow: 0 2px 10px rgba(255, 111, 0, 0.1);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .review-user-info {
            flex: 1;
        }

        .review-user-name {
            font-weight: bold;
            color: #4a2c0f;
            margin-bottom: 0.25rem;
        }

        .review-matricula {
            color: #666;
            font-size: 0.9rem;
        }

        .review-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .review-stars {
            color: #ff6f00;
            font-size: 1.2rem;
        }

        .review-date {
            color: #666;
            font-size: 0.9rem;
        }

        .review-comment {
            color: #333;
            line-height: 1.5;
            margin-bottom: 1rem;
            font-style: italic;
        }

        .review-actions {
            display: flex;
            justify-content: flex-end;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .no-reviews {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 3rem;
            font-size: 1.2rem;
        }

        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .btn-confirm {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .review-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .rating-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container container-fluid my-5">
        <h1 class="page-title">Gestión de Reseñas</h1>
        <p class="subtitle lead">Panel de administración para todas las reseñas</p>

        <?php if (isset($mensaje_exito)): ?>
            <div class="message success alert alert-success" role="alert"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <?php if (isset($mensaje_error)): ?>
            <div class="message error alert alert-danger" role="alert"><?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <div class="stats-section p-4 shadow-sm">
            <h2 class="text-center mb-4" style="color: #ff6f00;">Estadísticas Generales</h2>
            
           <div class="stats-grid row g-4 text-center">
            <div class="col-md-6">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $stats['total']; ?></span>
                        <div class="stat-label">Total de Reseñas</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="stat-card">
                        <span class="stat-number"><?php echo $stats['promedio']; ?></span>
                        <div class="stat-label">Calificación Promedio</div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="stat-card">
                        <span class="stat-number">
                            <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= round($stats['promedio']) ? '★' : '☆';
                            }
                            ?>
                        </span>
                        <div class="stat-label">Estrellas Promedio</div>
                    </div>
                </div>
            </div>

            <?php if ($stats['total'] > 0): ?>
                <div class="rating-breakdown mt-4">
                    <h3 class="mt-4 mb-3" style="color: #ff6f00;">Distribución de Calificaciones</h3>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <div class="rating-row">
                            <div class="rating-stars">
                                <?php 
                                for ($j = 1; $j <= 5; $j++) {
                                    echo $j <= $i ? '★' : '☆';
                                }
                                ?>
                            </div>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: <?php echo $stats['total'] > 0 ? ($stats['por_estrella'][$i] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                            <div class="rating-count badge bg-secondary"><?php echo $stats['por_estrella'][$i]; ?></div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="reviews-section p-4 shadow-sm mt-5">
            <div class="reviews-header border-bottom pb-3 mb-4">
                <h2 class="reviews-title">Todas las Reseñas</h2>
                <span class="reviews-count badge bg-primary fs-6"><?php echo count($todas_resenas); ?> reseña(s)</span>
            </div>

            <?php if (empty($todas_resenas)): ?>
                <div class="no-reviews alert alert-info">
                    No hay reseñas disponibles en el sistema.
                </div>
            <?php else: ?>
                <?php foreach ($todas_resenas as $resena): ?>
                    <div class="review-card border p-3 mb-3 bg-light">
                        <div class="review-header d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                            <div class="review-user-info">
                                <div class="review-user-name h5 mb-0">
                                    <?php echo htmlspecialchars($resena['nombre'] ?? 'Usuario no encontrado'); ?>
                                </div>
                                <div class="review-matricula text-muted">
                                    Matrícula: <?php echo htmlspecialchars($resena['matricula']); ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="review-rating">
                                    <div class="review-stars">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $resena['calificación'] ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                                    <span class="badge bg-warning text-dark"><?php echo $resena['calificación']; ?>/5</span>
                                </div>
                                <div class="review-date text-sm text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($resena['fecha'])); ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($resena['comentario'])): ?>
                            <div class="review-comment p-2 border-start border-3 border-secondary bg-white">
                                <em>"<?php echo htmlspecialchars($resena['comentario']); ?>"</em>
                            </div>
                        <?php endif; ?>

                        <div class="review-actions mt-3">
                            <button class="btn-delete btn btn-sm btn-danger" onclick="confirmarEliminacion(<?php echo $resena['id_reseña']; ?>)">
                                Eliminar Reseña
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="confirm-modal" id="confirmModal">
        <div class="modal-content shadow-lg">
            <h3 class="text-danger mb-3">Confirmar Eliminación</h3>
            <p class="mb-4">¿Estás seguro de que deseas eliminar esta reseña? Esta acción no se puede deshacer.</p>
            <div class="modal-buttons">
                <button class="btn-confirm btn btn-danger" onclick="eliminarResena()">Eliminar</button>
                <button class="btn-cancel btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="eliminar_resena" value="1">
        <input type="hidden" name="id_resena" id="resenaIdInput">
    </form>

    <script>
        let resenaIdAEliminar = null;

        function confirmarEliminacion(idResena) {
            resenaIdAEliminar = idResena;
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('confirmModal').style.display = 'none';
            resenaIdAEliminar = null;
        }

        function eliminarResena() {
            if (resenaIdAEliminar) {
                document.getElementById('resenaIdInput').value = resenaIdAEliminar;
                document.getElementById('deleteForm').submit();
            }
        }

        window.onclick = function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target === modal) {
                cerrarModal();
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <?php include 'pie.html'; ?>
</body>
</html>