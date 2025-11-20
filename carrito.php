<?php
session_start();
require_once 'conexion.php';

// Require user login to view cart
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || !isset($_SESSION['matricula'])) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;
if (!empty($cart)) {
    $ids = array_keys($cart);
    $ids_int = array_map('intval', $ids);
    $in = implode(',', $ids_int);
    $sql = "SELECT id_producto, nombre, precio, imagen_url FROM productos WHERE id_producto IN ($in)";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $pid = $row['id_producto'];
            $qty = intval($cart[$pid]);
            $sub = floatval($row['precio']) * $qty;
            $items[$pid] = ['id'=>$pid,'nombre'=>$row['nombre'],'precio'=>floatval($row['precio']),'qty'=>$qty,'sub'=>$sub,'img'=>$row['imagen_url']];
            $total += $sub;
        }
    }
}

include 'encabezado_con_sesion.php';
?>
<main class="container my-4">
    <h2>Carrito</h2>
    <?php if (empty($items)): ?>
        <div class="alert alert-info">Tu carrito está vacío.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody id="cartBody">
                <?php foreach ($items as $it): ?>
                <tr data-id="<?= $it['id'] ?>">
                    <td><?= htmlspecialchars($it['nombre']) ?></td>
                    <td>$<?= number_format($it['precio'],2) ?></td>
                    <td><input type="number" min="1" value="<?= $it['qty'] ?>" onchange="updateQty(<?= $it['id'] ?>, this.value)"></td>
                    <td>$<?= number_format($it['sub'],2) ?></td>
                    <td><button class="btn btn-sm btn-danger" onclick="removeItem(<?= $it['id'] ?>)">Eliminar</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center">
            <h4>Total: $<span id="cartTotal"><?= number_format($total,2) ?></span></h4>
            <form method="post" action="checkout.php">
                <button class="btn btn-success">Realizar pedido</button>
            </form>
        </div>
    <?php endif; ?>
</main>

<script>
function apiPost(body) {
    return fetch('cart_api.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: body
    }).then(r => r.json());
}

function updateQty(pid, qty) {
    apiPost('action=update&product_id=' + pid + '&qty=' + qty).then(j => { if (j.success) location.reload(); else alert(j.message||'Error'); });
}
function removeItem(pid) {
    apiPost('action=remove&product_id=' + pid).then(j => { if (j.success) location.reload(); else alert(j.message||'Error'); });
}
</script>

<?php include 'pie.html'; ?>