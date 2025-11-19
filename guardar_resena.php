<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || !isset($_SESSION['matricula'])) {
    header('Location: login.php');
    exit;
}

$matricula = $_SESSION['matricula'];
$calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 0;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

if ($calificacion < 1 || $calificacion > 5 || $comentario === '') {
    header('Location: menu.php?resena=error');
    exit;
}

$stmt = $conn->prepare('INSERT INTO `reseñas` (`matricula`, `calificación`, `comentario`) VALUES (?, ?, ?)');
if (!$stmt) {
    header('Location: menu.php?resena=error');
    exit;
}
$stmt->bind_param('sis', $matricula, $calificacion, $comentario);
if ($stmt->execute()) {
    header('Location: menu.php?resena=ok');
    exit;
} else {
    header('Location: menu.php?resena=error');
    exit;
}

?>