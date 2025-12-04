<?php
session_start();
include 'conexion.php';

$usuario_autenticado = isset($_SESSION['usuario']) || isset($_SESSION['nombre']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviar_resena']) && $usuario_autenticado) {
    $matricula = $_SESSION['matricula'];
    $calificacion = $_POST['calificacion'];
    $comentario = trim($_POST['comentario']);
    
    if ($calificacion >= 1 && $calificacion <= 5) {
        try {
            $stmt = $conn->prepare("INSERT INTO reseñas (matricula, calificación, comentario) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $matricula, $calificacion, $comentario);
            
            if ($stmt->execute()) {
                $mensaje_exito = "¡Gracias por tu reseña! Tu opinión es muy valiosa para nosotros.";
            } else {
                $mensaje_error = "Error al enviar la reseña. Inténtalo de nuevo.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $mensaje_error = "Error al procesar la reseña: " . $e->getMessage();
        }
    } else {
        $mensaje_error = "La calificación debe estar entre 1 y 5 estrellas.";
    }
}

$resenas_usuario = [];
if ($usuario_autenticado) {
    $stmt = $conn->prepare("SELECT calificación, comentario, fecha FROM reseñas WHERE matricula = ? ORDER BY fecha DESC");
    $stmt->bind_param("s", $_SESSION['matricula']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $resenas_usuario[] = $row;
    }
    $stmt->close();
}

$todas_resenas = [];
$stmt_todas = $conn->prepare("
    SELECT r.calificación, r.comentario, r.fecha, u.nombre, u.matricula 
    FROM reseñas r 
    JOIN usuarios u ON r.matricula = u.matricula 
    ORDER BY r.fecha DESC
");
$stmt_todas->execute();
$result_todas = $stmt_todas->get_result();
while ($row = $result_todas->fetch_assoc()) {
    $todas_resenas[] = $row;
}
$stmt_todas->close();

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

if ($usuario_autenticado) {
    include 'encabezado_con_sesion.php';
} else {
    include 'encabezado_sin_sesion.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas - Politaste Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .page-title {
            color: #ff6f00; 
            font-weight: bold;
        }

        .custom-orange {
            color: #ff6f00;
        }
        
        .custom-bg-light {
            background-color: #f8f9fa;
        }

        .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star.active {
            color: #ff6f00;
        }
    </style>
</head>
<body class="custom-bg-light">
    <div class="container py-4">
        <h1 class="page-title text-center mb-1"><i class="bi bi-chat-square-text-fill"></i> Reseñas y Opiniones</h1>
        <p class="text-center text-muted mb-4">Comparte tu experiencia con Politaste Hub</p>

        <?php if (isset($mensaje_exito)): ?>
            <div class="alert alert-success text-center" role="alert"><i class="bi bi-check-circle-fill"></i> <?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <?php if (isset($mensaje_error)): ?>
            <div class="alert alert-danger text-center" role="alert"><i class="bi bi-x-octagon-fill"></i> <?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title custom-orange text-center mb-3 fs-4"><i class="bi bi-bar-chart-fill"></i> Calificación General</h2>
                <div class="text-center">
                    <div class="fs-1 fw-bold custom-orange"><?php echo $stats['promedio']; ?></div>
                    <div class="fs-4 custom-orange mb-1">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= round($stats['promedio']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                        }
                        ?>
                    </div>
                    <p class="text-muted">Basado en <?php echo $stats['total']; ?> reseña(s)</p>
                </div>
            </div>
        </div>

        <?php if ($usuario_autenticado): ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h2 class="custom-orange mb-3 fs-4"><i class="bi bi-pencil-square"></i> Deja tu Reseña</h2>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Calificación:</label>
                        <div class="d-flex align-items-center">
                            <div class="stars" id="starRating">
                                <span class="star" data-rating="1"><i class="bi bi-star-fill"></i></span>
                                <span class="star" data-rating="2"><i class="bi bi-star-fill"></i></span>
                                <span class="star" data-rating="3"><i class="bi bi-star-fill"></i></span>
                                <span class="star" data-rating="4"><i class="bi bi-star-fill"></i></span>
                                <span class="star" data-rating="5"><i class="bi bi-star-fill"></i></span>
                            </div>
                            <span class="text-muted fst-italic ms-3" id="ratingText">Selecciona una calificación</span>
                        </div>
                        <input type="hidden" name="calificacion" id="calificacionInput" required>
                    </div>

                    <div class="mb-3">
                        <label for="comentario" class="form-label fw-bold text-dark">Comentario (opcional):</label>
                        <textarea name="comentario" id="comentario" class="form-control" placeholder="Cuéntanos sobre tu experiencia..." rows="3"></textarea>
                    </div>

                    <button type="submit" name="enviar_resena" class="btn btn-warning btn-lg w-100 fw-bold">
                        <i class="bi bi-send-fill"></i> Enviar Reseña
                    </button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <h2 class="custom-orange mb-3 fs-4"><i class="bi bi-box-arrow-in-right"></i> ¿Quieres dejar una reseña?</h2>
                <p class="text-muted mb-0">
                    Debes <a href="login.php" class="text-decoration-none fw-bold custom-orange">iniciar sesión</a> 
                    para poder compartir tu experiencia con nosotros.
                </p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($usuario_autenticado && !empty($resenas_usuario)): ?>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h2 class="custom-orange mb-3 fs-4 text-center"><i class="bi bi-person-lines-fill"></i> Tus Reseñas Anteriores</h2>
                    <?php foreach ($resenas_usuario as $resena): ?>
                        <div class="border-bottom py-3">
                            <div class="custom-orange fs-5 mb-1">
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $resena['calificación'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                }
                                ?>
                            </div>
                            <?php if (!empty($resena['comentario'])): ?>
                                <p class="text-dark mb-1"><?php echo htmlspecialchars($resena['comentario']); ?></p>
                            <?php endif; ?>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($resena['fecha'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow mt-4">
            <div class="card-body">
                <h2 class="custom-orange mb-4 fs-4 text-center">
                    <i class="bi bi-people-fill"></i> Todas las Reseñas de la Comunidad 
                    <span class="text-muted fs-6 fw-normal">(<?php echo count($todas_resenas); ?> reseña<?php echo count($todas_resenas) != 1 ? 's' : ''; ?>)</span>
                </h2>
                
                <?php if (!empty($todas_resenas)): ?>
                    <?php foreach ($todas_resenas as $resena): ?>
                        <div class="p-3 mb-3 border-start border-4 border-warning bg-light rounded-end shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <div class="d-flex align-items-center me-2">
                                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        <?php 
                                        $nombres = explode(' ', trim($resena['nombre']));
                                        $iniciales = '';
                                        foreach ($nombres as $nombre) {
                                            if (!empty($nombre)) {
                                                $iniciales .= strtoupper(substr($nombre, 0, 1));
                                                if (strlen($iniciales) >= 2) break;
                                            }
                                        }
                                        echo $iniciales;
                                        ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($resena['nombre']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($resena['matricula']); ?></div>
                                    </div>
                                </div>
                                <div class="text-muted small mt-2 mt-md-0">
                                    <i class="bi bi-clock"></i> <?php echo date('d/m/Y H:i', strtotime($resena['fecha'])); ?>
                                </div>
                            </div>
                            
                            <div class="custom-orange fs-5 mb-2 d-flex align-items-center">
                                <span>
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $resena['calificación'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                    }
                                    ?>
                                </span>
                                <span class="text-muted small ms-2">(<?php echo $resena['calificación']; ?>/5)</span>
                            </div>
                            
                            <p class="text-dark mb-0 <?php echo empty($resena['comentario']) ? 'text-muted fst-italic' : ''; ?>">
                                <?php 
                                if (!empty($resena['comentario'])) {
                                    echo htmlspecialchars($resena['comentario']);
                                } else {
                                    echo "Sin comentarios adicionales";
                                }
                                ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted p-4">
                        <i class="bi bi-exclamation-circle-fill fs-4"></i> Aún no hay reseñas públicas. ¡Sé el primero en compartir tu experiencia!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($usuario_autenticado): ?> 
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('calificacionInput');
            const ratingText = document.getElementById('ratingText');
            
            const ratingTexts = {
                1: '1 - Muy malo',
                2: '2 - Malo',
                3: '3 - Regular',
                4: '4 - Bueno',
                5: '5 - Excelente'
            };

            const updateStars = (rating) => {
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                    s.style.color = index < rating ? '#ff6f00' : '#ddd';
                });
            };

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    ratingInput.value = rating;
                    ratingText.textContent = ratingTexts[rating];
                    updateStars(rating);
                });

                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.dataset.rating);
                    stars.forEach((s, index) => {
                        s.style.color = index < rating ? '#ff6f00' : '#ddd';
                    });
                });
            });

            document.getElementById('starRating').addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                updateStars(currentRating);
                ratingText.textContent = currentRating > 0 ? ratingTexts[currentRating] : 'Selecciona una calificación';
            });
            <?php endif; ?>
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <?php include 'pie.html'; ?>
</body>
</html>