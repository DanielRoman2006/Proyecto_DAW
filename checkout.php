<?php
session_start();
require_once 'conexion.php';

// Require login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || !isset($_SESSION['matricula'])) {
    header('Location: login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: carrito.php?error=empty');
    exit;
}

$ids = array_keys($cart);
$ids_int = array_map('intval', $ids);
$in = implode(',', $ids_int);
$sql = "SELECT id_producto, precio FROM productos WHERE id_producto IN ($in)";
$res = $conn->query($sql);
if (!$res) { header('Location: carrito.php?error=db'); exit; }

$total = 0;
$items = [];
while ($row = $res->fetch_assoc()) {
    $pid = $row['id_producto'];
    $price = floatval($row['precio']);
    $qty = intval($cart[$pid]);
    $sub = $price * $qty;
    $items[$pid] = ['price'=>$price,'qty'=>$qty,'sub'=>$sub];
    $total += $sub;
}

// Insert pedido
$numero_orden = strtoupper(uniqid('ORD'));
$stmt = $conn->prepare('INSERT INTO pedidos (numero_orden, matricula, estado_pedido, total) VALUES (?, ?, ?, ?)');
$estado = 'pendiente';
$stmt->bind_param('sssd', $numero_orden, $_SESSION['matricula'], $estado, $total);
if (!$stmt->execute()) { header('Location: carrito.php?error=db'); exit; }
$pedido_id = $conn->insert_id;

$stmt2 = $conn->prepare('INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)');
foreach ($items as $pid=>$d) {
    $qty = $d['qty'];
    $price = $d['price'];
    $sub = $d['sub'];
    $stmt2->bind_param('iiidd', $pedido_id, $pid, $qty, $price, $sub);
    $stmt2->execute();
}

// clear cart
unset($_SESSION['cart']);

header('Location: pedidos.php?created=1');
exit;
?>