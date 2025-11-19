<?php
require_once 'conexion.php';

$sql = "SELECT id_producto, nombre, descripcion, precio FROM productos WHERE disponible = 1";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo '
        <div class="col-md-6 col-lg-4">
            <div class="card card-menu">
                <div class="card-body">
                    <h5 class="card-title" style="color:#ff6f00;">' . htmlspecialchars($row['nombre']) . '</h5>
                    <p class="card-text">' . htmlspecialchars($row['descripcion']) . '</p>
                    <h6 style="color:#ff6f00; font-weight:bold;">$' . number_format($row['precio'], 2) . '</h6>
                </div>
            </div>
        </div>
        ';
    }
} else {
    echo '<div class="col-12 text-muted">No hay menú disponible</div>';
}
?>
