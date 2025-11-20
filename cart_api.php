<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? null;
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function cart_qty_total() {
    $total = 0;
    foreach ($_SESSION['cart'] as $q) $total += intval($q);
    return $total;
}

if (!$action) {
    echo json_encode(['success'=>false, 'message'=>'No action']);
    exit;
}

switch ($action) {
    case 'add':
        $product_id = intval($_POST['product_id'] ?? 0);
        $qty = intval($_POST['qty'] ?? 1);
        if ($product_id <= 0 || $qty <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid params']); exit; }
        $stmt = $conn->prepare('SELECT id_producto, precio FROM productos WHERE id_producto = ? AND disponible = 1');
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$res || $res->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'Producto no disponible']); exit; }
        if (!isset($_SESSION['cart'][$product_id])) $_SESSION['cart'][$product_id] = 0;
        $_SESSION['cart'][$product_id] += $qty;
        echo json_encode(['success'=>true,'cart_count'=>cart_qty_total()]);
        exit;
    case 'remove':
        $product_id = intval($_POST['product_id'] ?? 0);
        if ($product_id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid params']); exit; }
        unset($_SESSION['cart'][$product_id]);
        echo json_encode(['success'=>true,'cart_count'=>cart_qty_total()]);
        exit;
    case 'update':
        $product_id = intval($_POST['product_id'] ?? 0);
        $qty = intval($_POST['qty'] ?? 0);
        if ($product_id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid params']); exit; }
        if ($qty <= 0) { unset($_SESSION['cart'][$product_id]); }
        else { $_SESSION['cart'][$product_id] = $qty; }
        echo json_encode(['success'=>true,'cart_count'=>cart_qty_total()]);
        exit;
    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['success'=>true,'cart_count'=>0]);
        exit;
    case 'count':
        echo json_encode(['success'=>true,'cart_count'=>cart_qty_total()]);
        exit;
    case 'list':
        $cart = $_SESSION['cart'];
        if (empty($cart)) { echo json_encode(['success'=>true, 'items'=>[], 'cart_count'=>0]); exit; }
        $ids = array_keys($cart);
        $ids_int = array_map('intval', $ids);
        $in = implode(',', $ids_int);
        $sql = "SELECT id_producto, nombre, precio, imagen_url FROM productos WHERE id_producto IN ($in)";
        $res = $conn->query($sql);
        $items = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $pid = $row['id_producto'];
                $qty = intval($cart[$pid]);
                $items[] = ['id'=>$pid,'nombre'=>$row['nombre'],'precio'=>floatval($row['precio']),'qty'=>$qty,'sub'=>floatval($row['precio'])*$qty];
            }
        }
        echo json_encode(['success'=>true,'cart_count'=>cart_qty_total(),'items'=>$items]);
        exit;
    default:
        echo json_encode(['success'=>false,'message'=>'Unknown action']);
        exit;
}
?>