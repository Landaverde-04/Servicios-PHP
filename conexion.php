<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "tours_gpo4";

$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "resultado" => "error",
        "mensaje"   => "Fallo la conexion a la base de datos: " . $conn->connect_error
    ]);
    exit;
}
?>