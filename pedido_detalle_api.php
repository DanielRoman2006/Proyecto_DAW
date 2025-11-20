<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['role']) || !isset($_SESSION['matricula'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Not authenticated']);
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid id']); exit; }

$role = $_SESSION['role'];
if ($role !== 'admin') {
    $stmtChk = $conn->prepare('SELECT matricula FROM pedidos WHERE id_pedido = ?');
    $stmtChk->bind_param('i', $id);
    $stmtChk->execute();
    $resChk = $stmtChk->get_result();
    if (!$resChk || $resChk->num_rows === 0) { echo json_encode(['success'=>false,'message'=>'Pedido no encontrado']); exit; }
    $row = $resChk->fetch_assoc();
    if ($row['matricula'] !== $_SESSION['matricula']) { echo json_encode(['success'=>false,'message'=>'Access denied']); exit; }
}

$stmt = $conn->prepare('SELECT dp.id_producto, p.nombre, dp.cantidad, dp.precio_unitario, dp.subtotal FROM detalle_pedido dp JOIN productos p ON dp.id_producto = p.id_producto WHERE dp.id_pedido = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$items = [];
while ($r = $res->fetch_assoc()) {
    $items[] = ['id'=>$r['id_producto'],'nombre'=>$r['nombre'],'cantidad'=>intval($r['cantidad']),'precio'=>floatval($r['precio_unitario']??$r['precio']),'subtotal'=>floatval($r['subtotal'])];
}

echo json_encode(['success'=>true,'items'=>$items]);
exit;
?>