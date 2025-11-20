<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'proyecto_web'; 

$conn = new mysqli($host, $user, $pass, $db,);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

/**
 * Devuelve el nombre de la columna de contraseña para una tabla dada.
 * Busca por orden preferente: 'contraseña', 'contrasena', 'password', 'pass', 'clave'.
 * Retorna el nombre de la columna si existe, o null si no se encuentra.
 */
function getPasswordColumn($conn, $tableName) {
    $candidates = ['contraseña', 'contrasena', 'password', 'pass', 'clave'];
    foreach ($candidates as $c) {
        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . mysqli_real_escape_string($conn, $tableName) . "' AND COLUMN_NAME = '" . mysqli_real_escape_string($conn, $c) . "' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) return $c;
    }
    return null;
}


?>