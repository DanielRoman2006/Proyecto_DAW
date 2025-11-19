<?php
require_once 'conexion.php';
header('Content-Type: text/html; charset=utf-8');

$sql = "SELECT r.`calificación` AS cal, r.`comentario` AS comentario, r.`fecha` AS fecha, r.`matricula` AS matricula, IFNULL(u.nombre, '') AS nombre
        FROM `reseñas` r
        LEFT JOIN usuarios u ON u.matricula = r.matricula
        ORDER BY r.fecha DESC
        LIMIT 100";

$res = $conn->query($sql);
if (!$res) {
    echo '<div class="text-muted">No se pudieron cargar las reseñas.</div>';
    exit;
}

if ($res->num_rows === 0) {
    echo '<div class="text-muted">Aún no hay reseñas. Sé el primero en opinar.</div>';
    exit;
}

echo '<div class="list-group" style="max-height:300px;overflow:auto;">';
while ($row = $res->fetch_assoc()) {
    $cal = intval($row['cal']);
    $coment = htmlspecialchars($row['comentario']);
    $fecha = htmlspecialchars($row['fecha']);
    $nombre = $row['nombre'] ? htmlspecialchars($row['nombre']) : htmlspecialchars($row['matricula']);

    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $cal ? '★' : '☆';
    }

    echo '<div class="list-group-item">';
    echo '<div class="d-flex w-100 justify-content-between">';
    echo "<h6 class=\"mb-1\">{$nombre}</h6>";
    echo "<small class=\"text-muted\">{$fecha}</small>";
    echo '</div>';
    echo "<p class=\"mb-1\">{$coment}</p>";
    echo "<small style=\"color:#FF8F00;\">{$stars}</small>";
    echo '</div>';
}
echo '</div>';

?>
