<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT IDCLASE FROM CLASE_PASAJERO";
$resultado = $conn->query($sql);

$idsEnServidor = array();
if ($resultado) {
    while ($reg = $resultado->fetch_assoc()) {
        $idsEnServidor[] = $reg['IDCLASE']; // Guardamos solo el texto del ID
    }
}

echo json_encode($idsEnServidor);
$conn->close();
?>