<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'proyecto_web'; 

$conn = new mysqli($host, $user, $pass, $db, '3350');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


?>
