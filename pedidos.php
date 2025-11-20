<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['role']) || !isset($_SESSION['matricula'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$matricula = $_SESSION['matricula'];

if ($role === 'admin') {
    $sql = "SELECT id_pedido, numero_orden, matricula, fecha_hora_pedido, estado_pedido, total FROM pedidos ORDER BY fecha_hora_pedido DESC";
} else {
    $sql = "SELECT id_pedido, numero_orden, matricula, fecha_hora_pedido, estado_pedido, total FROM pedidos WHERE matricula = ? ORDER BY fecha_hora_pedido DESC";
}

$stmt = $conn->prepare($sql);
if ($role !== 'admin') {
    $stmt->bind_param('s', $matricula);
}
$stmt->execute();
$res = $stmt->get_result();

$orders = [];
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}

include 'encabezado_con_sesion.php';
?>

<main class="container my-4">
    <h2>Pedidos</h2>

    <?php if (isset($_GET['created']) && $_GET['created'] == 1): ?>
        <div class="alert alert-success">Pedido creado correctamente. Ver su historial abajo.</div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">No se encontraron pedidos.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Fecha</th>
                    <th>Matricula</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr id="order-row-<?= $order['id_pedido'] ?>">
                    <td><?= htmlspecialchars($order['numero_orden']) ?></td>
                    <td><?= htmlspecialchars($order['fecha_hora_pedido']) ?></td>
                    <td><?= htmlspecialchars($order['matricula']) ?></td>
                    <td><?= htmlspecialchars($order['estado_pedido']) ?></td>
                    <td>$<?= number_format(floatval($order['total']), 2) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="toggleDetails(<?= $order['id_pedido'] ?>)">Ver detalle</button>
                    </td>
                </tr>
                <tr id="order-details-<?= $order['id_pedido'] ?>" style="display:none;">
                    <td colspan="6">
                        <div class="order-detail-content" id="detail-<?= $order['id_pedido'] ?>">Cargando...</div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<script>
function toggleDetails(id) {
    const detailsRow = document.getElementById('order-details-' + id);
    const contentDiv = document.getElementById('detail-' + id);
    if (!detailsRow) return;
    if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
        detailsRow.style.display = '';
        if (contentDiv && contentDiv.textContent.trim() === 'Cargando...') {
            fetch('pedido_detalle_api.php?id=' + id)
            .then(r => r.json())
            .then(j => {
                if (j.success) {
                    const items = j.items;
                    let html = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr></thead><tbody>';
                    items.forEach(it => {
                        html += `<tr><td>${escapeHtml(it.nombre)}</td><td>$${Number(it.precio).toFixed(2)}</td><td>${it.cantidad}</td><td>$${Number(it.subtotal).toFixed(2)}</td></tr>`;
                    });
                    html += '</tbody></table>';
                    contentDiv.innerHTML = html;
                } else {
                    contentDiv.innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>';
                }
            }).catch(err => { contentDiv.innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>'; });
        }
    } else {
        detailsRow.style.display = 'none';
    }
}
function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>"']/g, function(match) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[match];
    });
}
</script>

<?php include 'pie.html'; ?>
