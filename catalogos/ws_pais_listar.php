<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT IDPAIS, NOMBREPAIS
        FROM PAIS
        ORDER BY NOMBREPAIS ASC";

$resultado = $conn->query($sql);

$filas = array();

while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}

echo json_encode($filas, JSON_UNESCAPED_UNICODE);

$conn->close();
?>