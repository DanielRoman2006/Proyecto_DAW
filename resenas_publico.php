<?php
require_once 'conexion.php';

$sql = "SELECT r.calificación, r.comentario, r.fecha, IFNULL(u.nombre, 'Anónimo') AS nombre
        FROM reseñas r
        LEFT JOIN usuarios u ON u.matricula = r.matricula
        ORDER BY r.fecha DESC";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    echo '<div class="card" style="box-shadow:0 2px 8px rgba(0,0,0,0.1);">';
    echo '<div class="card-body">';
    while ($row = $res->fetch_assoc()) {
        $stars = str_repeat('★', $row['calificación']) . str_repeat('☆', 5 - $row['calificación']);
        echo '
        <div style="border-bottom:1px solid #eee; padding:1rem 0;">
            <h6 style="color:#ff6f00; margin:0;">' . $row['nombre'] . '</h6>
            <small class="text-muted">' . $row['fecha'] . '</small>
            <p style="margin:0.5rem 0; color:#ff6f00;">' . $stars . '</p>
            <p style="margin:0; color:#666;">' . $row['comentario'] . '</p>
        </div>
        ';
    }
    echo '</div></div>';
} else {
    echo '<div class="text-muted">No hay reseñas aún</div>';
}
?>
