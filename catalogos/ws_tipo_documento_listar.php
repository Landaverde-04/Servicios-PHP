<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT IDTIPODOC, DESTIPODOC, LONGITUDMIN, LONGITUDMAX,
               REGEXVALIDACION, FORMATOEJEMPLO, MENSAJEERROR, ACTIVO
        FROM TIPO_DOCUMENTO
        ORDER BY DESTIPODOC ASC";

$resultado = $conn->query($sql);

$filas = array();

while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}

echo json_encode($filas, JSON_UNESCAPED_UNICODE);

$conn->close();
?>