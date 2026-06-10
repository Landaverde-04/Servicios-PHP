<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT IDTURISTA, NOMBRES, APELLIDOS FROM TURISTA ORDER BY NOMBRES ASC";
$resultado = $conn->query($sql);

$filas = array();
if ($resultado) {
    while ($reg = $resultado->fetch_assoc()) {
        $filas[] = $reg;
    }
}
echo json_encode($filas);
$conn->close();
?>